<?php

namespace slowfoot\dev;

use slowfoot\project;

class images_server extends fileserver {

    public function __construct(project $project) {
        $root = $project->base . '/var/rendered-images';
        $rewrite = [];
        parent::__construct($root, $rewrite);
    }
}
