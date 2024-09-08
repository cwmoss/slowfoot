<?php

namespace slowfoot\functions;

require_once("./BasePathDetector.php");
require_once("./runner.php");
require_once("./request.php");
require_once("./response.php");

use Selective\BasePath\BasePathDetector;

$funbase = $_SERVER['DOCUMENT_ROOT'] . '/../server-functions';
$requesturl = $_SERVER['REQUEST_URI'];

$b = new BasePathDetector($_SERVER, PHP_SAPI);
$basepath = $b->getBasePath();
$request_path = preg_replace("~^{$basepath}~", "", $requesturl);
print "request_path: $request_path ~ $basepath ~ " . PHP_SAPI;
print_r($_SERVER);
exit;
$runner = new runner($funbase);
$runner->run($request_path);
