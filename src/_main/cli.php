<?php
$project_dir ??= "";

require $project_dir . '/vendor/autoload.php';

use slowfoot\document;
use slowfoot\setup;
use slowfoot\util\console;
use slowfoot\store;

error_reporting(E_ALL);
ini_set("display_errors", 0);
$slft_lib_base = dirname(__DIR__);

$doc = <<<DOC
slowfoot.

Usage:
  slowfoot dev [-S <server:port>] [-P <port>] [-f | --fetch <content source>] [-d <project directory>]
  slowfoot build [-f | --fetch <content source>] [-d <project directory>]
  slowfoot init [-d <project directory>]
  slowfoot preview [-d <project directory>]
  slowfoot (-h | --help)
  slowfoot fetch [-d <project directory>]
  slowfoot info [-d <project directory>]
  slowfoot starship [-d <project directory>]
  slowfoot --version

Options:
  -f                        fetch all contents
  --fetch <content source>  fetch contents from <content source>
  -h --help                 Show this screen.
  --version                 Show version.
  -S --server <server:port> Set server and port [default: localhost:1199]
  -P --port <port>          Set port only
  -d <project directory>    Set the project base directory

DOC;

//require_once(__DIR__.'/../vendor/autoload.php');

$parsed = Docopt::handle($doc, array('version' => 'slowfoot 0.1'));
#var_dump($parsed);
$args = $parsed->args;
#var_dump($args);

$ds = $dist = null;

// https://www.kammerl.de/ascii/AsciiSignature.php rounded

$logo = '
       _                ___
      | |              / __)            _
   ___| | ___  _ _ _ _| |__ ___   ___ _| |_
  /___) |/ _ \| | | (_   __) _ \ / _ (_   _)
 |___ | | |_| | | | | | | | |_| | |_| || |_
 (___/ \_)___/ \___/  |_|  \___/ \___/  \__)

 ';

if ($args['dev']) {
  print $logo . "\n";

  $FETCH = $args['-f'];
  $PDIR = $args['-d'];
  if ($PDIR && $PDIR[0] != '/') {
    $PDIR = SLOWFOOT_BASE . '/' . $PDIR;
  }
  define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);
  $dev_src = 'src/';
  if ($PDIR) {
    $dev_src = $PDIR . '/src/';
  }

  $devserver = explode(':', $args['--server'] ?? 'localhost:1199');
  if ($args['--port']) {
    $devserver[1] = $args['--port'];
  }
  $devserver = join(":", $devserver);

  // evtl. fetching data
  require $slft_lib_base . '/_boot.php';

  (new setup(SLF_PROJECT_DIR))->setup();

  print console::console_table(['_type' => 'type', 'total' => 'total'], $ds->info());

  // this wont work :)
  // `(sleep 1 ; open http://localhost:1199/ )&`;
  // this works!
  # automatisches öffnen gefällt mir nicht mehr
  # shell_exec('(sleep 1 ; open http://localhost:1199/ ) 2>/dev/null >/dev/null &');
  $command = "XXXPHP_CLI_SERVER_WORKERS=4 php -d short_open_tag=On -S {$devserver} -t {$dev_src} {$slft_lib_base}/_main/development.php";
  print "\n\n";

  print "starting development server\n\n";
  print "   🌈 http://$devserver\n\n";
  print "<cmd> click\n";
  print "have fun!\n\n";
  print $command . "\n";
  $wss = "php {$slft_lib_base}/wss.php " . SLOWFOOT_BASE;
  #shell_exec("$wss &");
  #print "end";
  `$command`;
  #`($command &) && ($wss &)`;
}
if ($args['build']) {
  print $logo . "\n";

  $FETCH = $args['-f'];
  $PDIR = $args['-d'];
  if ($PDIR && $PDIR[0] != '/') {
    $PDIR = SLOWFOOT_BASE . '/' . $PDIR;
  }
  define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);

  require $slft_lib_base . '/_boot.php';
  (new setup(SLF_PROJECT_DIR))->setup();
  require $slft_lib_base . '/cli/build.php';
}
//if ($args['info']) {
//  require $slft_lib_base . '/cli/info.php';
//}
if ($args['preview']) {
  $PDIR = $args['-d'];
  if ($PDIR && $PDIR[0] != '/') {
    $PDIR = SLOWFOOT_BASE . '/' . $PDIR;
  }
  define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);
  shell_info("starting testserver. you can review your build here.", true);
  $boot_only_config = true;
  require $slft_lib_base . '/_boot.php';
  $testserver = "localhost:11999";
  $command = "php -S {$testserver} -t {$dist}";
  print "\n";
  print "   🤟 http://$testserver\n\n";
  print "<cmd> click\n";
  print "have fun!\n\n";
  `$command`;
}
if ($args['fetch']) {
  $FETCH = true;
  $PDIR = $args['-d'];
  if ($PDIR && $PDIR[0] != '/') {
    $PDIR = SLOWFOOT_BASE . '/' . $PDIR;
  }
  define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);
  require $slft_lib_base . '/_boot.php';
}

if ($args['init']) {
  $PDIR = $args['-d'];
  if ($PDIR && $PDIR[0] != '/') {
    $PDIR = SLOWFOOT_BASE . '/' . $PDIR;
  }
  define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);
  require $slft_lib_base . '/cli/init.php';
}
if ($args['info']) {

  $d = new document("eins", "page", ["hu" => "bba"]);

  print_r($d);
  var_dump($d["_type"] ? true : false);
  exit;
  $PDIR = $args['-d'];
  if ($PDIR && $PDIR[0] != '/') {
    $PDIR = SLOWFOOT_BASE . '/' . $PDIR;
  }
  define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);
  $boot_only_config = false;
  $boot_quiet = true;
  require $slft_lib_base . '/_boot.php';
  print "🌈 slowfoot\n";

  (new setup(SLF_PROJECT_DIR))->setup();
  print console::console_table(['_type' => 'type', 'total' => 'total'], $ds->info());
}
if ($args['starship']) {
  $PDIR = $args['-d'];
  if ($PDIR && $PDIR[0] != '/') {
    $PDIR = SLOWFOOT_BASE . '/' . $PDIR;
  }
  define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);
  $boot_only_config = true;
  $boot_quiet = true;
  require $slft_lib_base . '/_boot.php';
  print "🌈 slft";

  if (file_exists(SLF_PROJECT_DIR . "/var/slowfoot.db")) {
    $db = new store\sqlite(["adapter" => "sqlite:" . SLF_PROJECT_DIR . "/var/slowfoot.db"]);
    $info = $db->info_line();
    printf(" %s docs, %s paths", $info[0], $info[1]);
  } else {
    print " no db";
  }
}
