<?php

namespace slowfoot;

use Exception;

class setup {

  public function __construct(public string $project_dir) {
  }

  public function setup(): bool {
    // TODO: inject somewhere
    $writebase = getenv("SLFT_WRITE_PATH");
    if ($writebase) $writebase = $this->project_dir . "/" . $writebase;
    else $writebase = $this->project_dir;

    $writeable = [
      'var',
      'var/download',
      'var/rendered-images',
      'var/template'
    ];

    foreach ($writeable as $dir) {
      $fdir = $writebase . '/' . $dir;
      dbg("+ setup check $fdir");
      if (!file_exists($fdir)) {
        mkdir($fdir);
      }

      if (!is_dir($fdir)) {
        throw new Exception("Could not create directory $fdir. Please make it a writeable directory.");
      }
      if (!is_writable($fdir)) {
        throw new Exception("Directory $fdir is not writeable.");
      }
    }
    return true;
  }

  public function init(string $projectname): array {
    $skipped = [];
    $projectbase = realpath($this->project_dir);
    $srcbase = realpath(__DIR__ . "/../resources/projects/$projectname");
    // print $srcbase . "\n";
    // return $skipped;
    foreach (globstar("$srcbase/**/*") as $file) {
      if (!is_file($file)) continue;
      $rel_name = \substr($file, mb_strlen($srcbase));
      $rel_dir = \dirname($rel_name);
      $rel_file = \basename($rel_name);
      if (str_starts_with($rel_file, "dot.")) {
        $rel_file = \substr($rel_file, 3);
      }
      $dest = \ltrim($rel_dir . "/" . $rel_file, "/");
      $destfile = $projectbase . "/" . $dest;
      if (file_exists($destfile)) {
        $skipped[] = $dest;
        continue;
      }
      if (!is_dir(dirname($destfile))) mkdir(dirname($destfile), recursive: true);
      copy($file, $destfile);
      // print $file . " => $dest => $destfile\n";
    }
    return $skipped;
  }

  public function webdeploy(): array {
    $skipped = [];
    $projectbase = realpath($this->project_dir);
    $sourcedir = realpath(__DIR__ . "/../webdeploy");
    $destdir = $projectbase . "/webdeploy";
    mkdir($destdir);
    $files = ["index.php"];
    foreach ($files as $file) {
      $destfile = $destdir . "/" . $file;
      if (file_exists($destfile)) {
        $skipped[] = "webdeploy/$file";
        continue;
      }
      copy($sourcedir . "/" . $file, $destfile);
    }
    return $skipped;
  }
}
