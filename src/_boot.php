<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
  require_once __DIR__ . '/../vendor/autoload.php';
} else {
  require_once __DIR__ . '/../../../autoload.php';
}

error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED);

use slowfoot\configuration;
use Dotenv\Dotenv;
use slowfoot\project;

ini_set('display_errors', 0);
if (!defined('SLOWFOOT_BASE')) {
  // via php cli webserver
  #    print_r($_SERVER);
  #    print_r($_SERVER);
  // different project path without vendor/ dir?
  // TODO: better ideas
  $internal = str_replace('vendor/cwmoss/slowfoot-lib/docs_src/src', '', $_SERVER['DOCUMENT_ROOT']);
  if ($internal == $_SERVER['DOCUMENT_ROOT']) {
    unset($internal);
  }
  define('SLOWFOOT_BASE', $_SERVER['DOCUMENT_ROOT'] . '/../');
} else {
}

new slowfoot\error_handler;

if (!defined('SLOWFOOT_PREVIEW')) {
  define('SLOWFOOT_PREVIEW', false);
}
if (!defined('SLOWFOOT_WEBDEPLOY')) {
  define('SLOWFOOT_WEBDEPLOY', false);
}
$base = SLOWFOOT_BASE;
// set a different directory as project base
// independent from ./vendor dir
if (isset($PDIR) && $PDIR) {
  $base = $PDIR;
}
if (file_exists("$base/.env")) {
  //print "env: $base/.env";
  Dotenv::createImmutable("$base")->load();
}

$_ENV = array_merge(getenv(), $_ENV);

$project = new project(configuration::load(
  $base,
  (isset($FETCH) && $FETCH),
  is_prod: $IS_PROD,
  write_path: $_ENV["SLFT_WRITE_PATH"] ?? ""
));

// for testserver
if ($boot_only_config ?? null) return $project;

$project->load();

if (!defined('PATH_PREFIX')) {
  if (PHP_SAPI == 'cli-server') {
    define('PATH_PREFIX', "");
  } else {
    define('PATH_PREFIX', $project->path_prefix());
  }
}

if (!(SLOWFOOT_PREVIEW || SLOWFOOT_WEBDEPLOY)) {
  #    require_once 'routing.php';
}

dbg('[dataset] info', $project->info());
return $project;
