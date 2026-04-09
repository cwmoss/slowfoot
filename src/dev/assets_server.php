<?php

namespace slowfoot\dev;

use slowfoot\project;

class assets_server extends fileserver {

    public function __construct(project $project) {
        $root = $project->src;
        $rewrite = [];
        parent::__construct($root, $rewrite);
    }
}
