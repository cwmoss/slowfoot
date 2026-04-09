<?php

namespace slowfoot\dev;

class ui_server extends fileserver {

    public function __construct() {
        $root = __DIR__ . '/../../ui';
        $rewrite = [];
        parent::__construct($root, $rewrite);
    }
}
