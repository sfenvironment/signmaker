<?php

namespace SF\Signmaker;

use Dompdf\Dompdf;

class Signmaker {
  public static function handle() {
    $configuration = static::getConfiguration();
    $output = '';
    ob_start(); ?>
    <html>
    <head>
        <link rel="stylesheet" href="<?php echo $configuration['root']; ?>/assets/css/style.css" >
        <script src="<?php echo $configuration['root']; ?>/assets/js/signmaker.js"></script>
        <style>
            #image-region {
                top: <?php echo $configuration['bbox'][0]; ?>%;
                right: <?php echo 100 - $configuration['bbox'][1]; ?>%;
                bottom: <?php echo 100 - $configuration['bbox'][2]; ?>%;
                left: <?php echo $configuration['bbox'][3]; ?>%;
            }
        </style>
        <script>
            window.configuration = <?php echo json_encode($configuration); ?>;
        </script>
    </head>
    <body>
    <main id="sign">
        <img id="background" src="<?php echo $configuration['directory'] . $configuration['background']; ?>" />
        <ul id="image-region"></ul>
    </main>
    <aside>
        <h1>Signmaker</h1>
        <ul id="image-menu"></ul>
        <button id="generate">Save PDF</button>
    </aside>
    </body>
    </html>
    <?php $output .= ob_get_clean();
    return $output;
  }

  public static function generate($html) {
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('letter', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    return $dompdf->stream();
  }

  public static function getConfiguration() {
      $root = urldecode($_GET['root']) ?? '';
    $directory = $_GET['directory'] ?? null;
    if (!$directory) {
      throw new \Exception('Directory not specified');
    }
    if (!is_dir($directory)) {
      throw new \Exception("{$directory} is not a directory");
    }
    $files = scandir($directory);
    $images = [];
    $background = $_GET['background'] ?? null;
    foreach ($files as $file) {
      if ($file === '.' || $file === '..') continue;
      $name = pathinfo($file, PATHINFO_FILENAME);
      if ('background' === $name && !$background) {
        $background = $file;
      } else {
        // TODO add https://github.com/shanept/MimeSniffer/
        $images[] = $file;
      }
    }
    if (!$background) {
      throw new \Exception("Background file not specified");
    }
    if (empty($files)) {
      throw new \Exception("No files specified");
    }
    $bbox = urldecode($_GET['bbox']) ?? null;
    if (!$bbox) {
      throw new \Exception('Bbox not specified');
    }
    $bbox = explode(',', $bbox);
    if (count($bbox) !== 4) {
      throw new \Exception('Bbox must be 4 percentages separated by commas: Top,Left,Bottom,Right');
    }
    $bbox = array_map('intval', $bbox);
    $configuration = [
      'directory' => '/' . trim($directory, '/') . '/',
      'bbox' => $bbox,
      'images' => $images,
      'background' => $background,
      'root' => $root,
    ];
    return $configuration;
  }
}