<?php

namespace slowfoot\dev;

use slowfoot\project;

class internal_assets_server extends fileserver {

    public function __construct() {
        $root = __DIR__ . '/../../resources';
        $rewrite = [];
        parent::__construct($root, $rewrite);
    }
}
