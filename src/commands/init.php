<?php

namespace slowfoot\commands;

use slowfoot\app;
use cwmoss\final_cli\cli;

class init {

    public function __construct(public app $app) {
    }

    // init [-d <project directory>] [--webdeploy] [--force]
    /**
     * init a project.
     * 
     * generates some folders and templates
     * to get you started
     */
    public function __invoke(
        #[cli("-d", "Set the project base directory")]
        ?string $project_directory = null,
        bool $webdeploy = false,
        bool $force = false
    ) {
        $setup = $this->app->setup_get();

        if ($webdeploy) {
            shell_info("copy webdeploy script to " . SLF_PROJECT_DIR);
            $skipped = $setup->webdeploy();
        } else {
            shell_info("initializing new project in " . SLF_PROJECT_DIR);
            if (!directory_is_empty(SLF_PROJECT_DIR) && !$force) {
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
