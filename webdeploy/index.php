<?php
/*
apache:
SetEnv SLFT_BUILD_KEY ycx-sdfsdf-sdf213213-ewrwe-dfs
Alias /__web-deploy /var/www/vhosts/kurparkverlag/slowfoot/web-deploy/index.php
*/

define('SLOWFOOT_START', microtime(true));
define('SLOWFOOT_BASE', dirname(__DIR__));

require SLOWFOOT_BASE . '/vendor/autoload.php';

require SLOWFOOT_BASE . '/vendor/cwmoss/slowfoot-lib/src/webdeploy.php';
