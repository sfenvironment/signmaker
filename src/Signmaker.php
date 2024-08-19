<?php

namespace SF\Signmaker;

class Signmaker {
  public static function handle() {
    $configuration = static::getConfiguration();
    return json_encode($configuration);
  }

  public static function getConfiguration() {
    $directory = $_GET['directory'] ?? null;
    if (!$directory) {
      throw new \Exception('Directory not specified');
    }
    if (!is_dir($directory)) {
      throw new \Exception("{$directory} is not a directory");
    }
    $files = scandir($directory);
    print_r($files);
    $bbox = $_GET['bbox'] ?? null;
    if (!$bbox) {
      throw new \Exception('Bbox not specified');
    }
    $bbox = explode(',', $bbox);
    if (count($bbox) !== 4) {
      throw new \Exception('Bbox must be 4 integers separated by commas');
    }
    $bbox = array_map('intval', $bbox);
    $configuration = [
      'directory' => $directory,
      'bbox' => $bbox,
    ];
    return $configuration;
  }
}