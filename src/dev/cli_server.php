<?php
/*
    entrypoint for cli-server

    PHP_CLI_SERVER_WORKERS=4 

    php -d variables_order=EGPCS -d short_open_tag=On -S 0.0.0.0:1199 -t project/src vendor_src/dev/cli_server.php

    ex:
    php -d variables_order=EGPCS -d short_open_tag=On -S 0.0.0.0:1199 -t docs/src src/dev/cli_server.php

    php -d variables_order=EGPCS -d short_open_tag=On -S 0.0.0.0:1199 -t ./src/ ../../slowfoot/src/dev/cli_server.php 
*/

namespace slowfoot\dev;

use slowfoot\app;
use slowfoot\project;
use FrameworkX\Container;
use FrameworkX\App as xapp;
use slowfoot\commands\dev_fx;

define('SLOWFOOT_START', microtime(true));

error_reporting(E_ALL ^ E_DEPRECATED);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../../../autoload.php';
}

// $bin_dir = $_composer_bin_dir ?? __DIR__;
// $project_dir = isset($_composer_bin_dir) ? dirname(dirname($bin_dir)) : dirname($bin_dir);

ini_set("display_errors", 0);

$project_dir = $_SERVER['DOCUMENT_ROOT'] . '/../';
// define('SLOWFOOT_BASE', $project_dir);

$app = (new app($project_dir, true, false))->load_project()->load_data(true);
$project = $app->project;

$container = new Container([
    // "X_LISTEN" => $devserver,
    // "X_EXPERIMENTAL_RUNNER" => HttpServerRunner::class,
    // \FrameworkX\ErrorHandler::class => fn() => new error(),
    project::class => $project
]);

$restart = false;
// dbg("make app");
$app = new xapp($container, new error(), timer::class);
// phpinfo();

dev_fx::add_routes($app, $project);

$app->run();
