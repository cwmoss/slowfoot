<?php
$project_dir ??= "";
error_reporting(E_ALL ^ E_DEPRECATED);
// error_reporting(E_ALL);
require $project_dir . '/vendor/autoload.php';

use slowfoot\document;
use slowfoot\setup;
use slowfoot\util\console;
use slowfoot\store;
use slowfoot\app;
use slowfoot\cli\init;
use slowfoot\cli\build;

ini_set("display_errors", 0);
$slft_lib_base = dirname(__DIR__);

$doc = <<<DOC
slowfoot.

Usage:
  slowfoot dev [-S <server:port>] [-P <port>] [-f | --fetch <content source>] [-d <project directory>] [--colors <ansi mode>] [-v]
  slowfoot build [-f | --fetch <content source>] [-d <project directory>] [--colors <ansi mode>] [-v]
  slowfoot init [-d <project directory>] [--webdeploy] [--force] [--colors <ansi mode>] [-v]
  slowfoot preview [-d <project directory>] [--colors <ansi mode>] [-v]
  slowfoot (-h | --help)
  slowfoot fetch [-d <project directory>] [--colors <ansi mode>] [-v]
  slowfoot info [-d <project directory>] [--colors <ansi mode>] [-v]
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
  --colors <ansi mode>      Set color mode auto, on, off [default: auto]
  -v                        Set verbose mode for debugging

DOC;

//require_once(__DIR__.'/../vendor/autoload.php');

$parsed = Docopt::handle($doc, array('version' => 'slowfoot 0.1'));
#var_dump($parsed);
$args = $parsed->args;
// var_dump($_SERVER);

$ds = $dist = null;
$IS_PROD = false;

// https://www.kammerl.de/ascii/AsciiSignature.php rounded

$logo = '
       _                ___
      | |              / __)            _
   ___| | ___  _ _ _ _| |__ ___   ___ _| |_
  /___) |/ _ \| | | (_   __) _ \ / _ (_   _)
 |___ | | |_| | | | | | | | |_| | |_| || |_
 (___/ \_)___/ \___/  |_|  \___/ \___/  \__)

 ';
$need_fetch = match (true) {
    $args['preview'], $args['init'], $args['info'], $args['starship'] => false,
    default => true
};

if ($need_fetch) {
    $FETCH = $args['-f'];
}

$PDIR = trim($args['-d'] ?? "");
$PDIR = match (true) {
    $PDIR == ".", $PDIR == "./" => getcwd(),
    $PDIR && $PDIR[0] != '/' => SLOWFOOT_BASE . '/' . $PDIR,
    default => $PDIR
};
define('SLF_PROJECT_DIR', $PDIR ?: $project_dir);


$colors = $args['--colors'];
// print "colors: $colors";
console::console($colors);

// if (!$args['-v']) define("SLOWFOOT_NO_DEBUG", 1);

$app = new app(SLF_PROJECT_DIR, $args['-v'], isset($FETCH) && $FETCH);

if ($args['dev']) {
    print $logo . "\n";

    $dev_src = 'src/';
    if ($PDIR) {
        $dev_src = $PDIR . '/src/';
    }

    $devhostport = explode(':', $args['--server'] ?? '0.0.0.0:1199', 2) +
        [1 => $args['--port'] ?? "1199"];

    $devserver = join(":", $devhostport);

    $app->setup()->load_data(true);

    // evtl. fetching data
    $project = $app->project;

    print console::console_table(['_type' => 'type', 'total' => 'total'], $project->ds->info());

    // this wont work :)
    // `(sleep 1 ; open http://localhost:1199/ )&`;
    // this works!
    # automatisches öffnen gefällt mir nicht mehr
    # shell_exec('(sleep 1 ; open http://localhost:1199/ ) 2>/dev/null >/dev/null &');
    $command = "XXXPHP_CLI_SERVER_WORKERS=4 php -d variables_order=EGPCS -d short_open_tag=On -S {$devserver} -t {$dev_src} {$slft_lib_base}/_main/development.php";
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
    $app->setup()->load_data(false);
    new build($app)->run($args);
}

if ($args['preview']) {
    shell_info("starting testserver. you can review your build here.", true);
    $testserver = "localhost:11999";
    $command = "php -S {$testserver} -t {$project->dist()}";
    print "\n";
    print "   🤟 http://$testserver\n\n";
    print "<cmd> click\n";
    print "have fun!\n\n";
    `$command`;
}
if ($args['fetch']) {
    $app->setup()->load_data(true);
}

if ($args['init']) {
    new init($app)->run($args);
}

if ($args['info']) {
    $boot_only_config = false;
    $boot_quiet = true;

    $app->setup()->load_data(true);

    print "🌈 slowfoot\n";
    print "=> $app->project_dir\n";

    print console::console_table(['_type' => 'type', 'total' => 'total'], $app->project->config->db->info());
}

if ($args['starship']) {

    $boot_only_config = true;
    $boot_quiet = true;
    print "🌈 slft";

    if (file_exists(SLF_PROJECT_DIR . "/var/slowfoot.db")) {
        $db = new store\sqlite(["adapter" => "sqlite:" . SLF_PROJECT_DIR . "/var/slowfoot.db"]);
        $info = $db->info_line();
        printf(" %s docs, %s paths", $info[0], $info[1]);
    } else {
        print " no db";
    }
}
