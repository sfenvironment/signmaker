<?php

namespace SF\Signmaker;

use Dompdf\Dompdf;
use Dompdf\Options;

class Signmaker {
  public static function handle() {
    $configuration = static::getConfiguration();
    $output = '';
    [$top, $right, $bottom, $left] = $configuration['bbox'];
    ob_start(); ?>
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
              "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo $configuration['root']; ?>/assets/css/style.css" >
        <?php foreach ($configuration['stylesheets'] as $stylesheet): ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $stylesheet; ?>">
        <?php endforeach; ?>
        <script src="<?php echo $configuration['root']; ?>/assets/js/signmaker.js"></script>
        <style>
            #image-region {
                top: <?php echo (($top / 100) * 8.5); ?>in;
                right: <?php echo ((1 - $right / 100) * 8.5); ?>in;
                bottom: <?php echo ((1 - $bottom / 100) * 8.5); ?>in;
                left: <?php echo (($left / 100) * 8.5); ?>in;
            }

            #image-region.children-1 .row,
            #image-region.children-2 .row,
            #image-region.children-3 .row,
            #image-region.children-4 .row {
                height: <?php echo 8.5 * (($bottom - $top) / 100); ?>in;
            }
            .row-2 {
                top: calc(<?php echo (8.5 * (($bottom - $top) / 100)) / 2; ?>in + 1px);
                height: <?php echo (8.5 * (($bottom - $top) / 100)) / 2; ?>in;
                position: absolute;
                left: 0;
            }
            #image-region.children-5 span,
            #image-region.children-6 span,
            #image-region.children-7 span,
            #image-region.children-8 span {
                height: <?php echo (8.5 * (($bottom - $top) / 100)) / 2; ?>in;
            }
        </style>
        <script>
            window.configuration = <?php echo json_encode($configuration); ?>;
        </script>
    </head>
    <body>
    <main id="sign">
        <img id="background" src="<?php echo $configuration['directory'] . $configuration['background']; ?>" />
        <div id="image-region"></div>
    </main>
    <aside>
        <span class="instruction"><span class="number">1.</span>Select items to make your sign</span>
        <ul id="image-menu"></ul>
        <span class="instruction"><span class="number">2.</span>Start over or save your sign</span>
        <fieldset>
            <button id="reset">Start Over</button>
            <button id="generate">Save PDF</button>
        </fieldset>
    </aside>
    </body>
    </html>
    <?php $output .= ob_get_clean();
    return $output;
  }

  public static function generate($html, $output = 'output', $additional_chroot = []) {
    $options = new Options();
    $options->setIsRemoteEnabled(true);
    $options->setChroot(array_merge([__DIR__ . '../assets/css'], $additional_chroot));
    $options->setDefaultMediaType('print');
//    $options->setDebugLayout(true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('letter', 'landscape');


    // Render the HTML as PDF
    $dompdf->render();

    if ('stream' == $output) {
        return $dompdf->stream();
    } else if ('output' === $output) {
        return $dompdf->output();
    }

    return $dompdf;
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
        $images[] = trim($file, '/');
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
    if (isset($_GET['stylesheets'])) {
        $stylesheets = array_map('urldecode', explode(',', $_GET['stylesheets']));
    } else {
        $stylesheets = [];
    }
    $configuration = [
      'directory' => trim($directory, '/') . '/',
      'bbox' => $bbox,
      'images' => $images,
      'background' => $background,
      'root' => $root,
      'stylesheets' => $stylesheets,
    ];
    return $configuration;
  }
}