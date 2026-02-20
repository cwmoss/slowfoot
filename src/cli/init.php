<?php

namespace slowfoot\cli;

use slowfoot\app;

class init {

    public function __construct(public app $app) {
    }

    public function run(array $args) {
        $setup = $this->app->setup_get();

        if ($args['--webdeploy']) {
            shell_info("copy webdeploy script to " . SLF_PROJECT_DIR);
            $skipped = $setup->webdeploy();
        } else {
            shell_info("initializing new project in " . SLF_PROJECT_DIR);
            if (!directory_is_empty(SLF_PROJECT_DIR) && !$args['--force']) {
                print "\ndirectory is not empty, aborting init\n" .
                    "  if you want to init anyways, use the --force flag\n";
                exit(1);
            }
            $skipped = $setup->init("minimal");
        }

        shell_info();

        if ($skipped) {
            shell_info("some files could not be created, because they are already there:", true);
            print_r($skipped);
        }
    }
}
