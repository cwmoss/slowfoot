<?php
/*
apache:
SetEnv SLFT_BUILD_KEY ycx-sdfsdf-sdf213213-ewrwe-dfs
Alias /__web-deploy /var/www/vhosts/kurparkverlag/slowfoot/web-deploy/index.php

caddy:
    handle_path /__webdeploy/* {
        root * /app/site/webdeploy/
        php_server
    }

request outside docker:

    curl -vv http://localhost:9901/__webdeploy/ -H 'x-slft-deploy: 1234'

*/

define('SLOWFOOT_START', microtime(true));
define('SLOWFOOT_BASE', dirname(__DIR__));

require SLOWFOOT_BASE . '/vendor/autoload.php';

require SLOWFOOT_BASE . '/vendor/cwmoss/slowfoot/src/_main/webdeploy.php';
