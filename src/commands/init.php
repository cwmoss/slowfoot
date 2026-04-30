<?php

namespace slowfoot\commands;

use slowfoot\app;
use cwmoss\final_cli\cli;

class init {

    public function __construct(public app $app) {
    }

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
        $terminal = new terminal;
        if ($webdeploy) {
            $terminal->shell_info("copy webdeploy script to " . $this->app->project_dir . "/webdeploy/");
            $skipped = $setup->webdeploy();
        } else {
            $terminal->shell_info("initializing new project in " . $this->app->project_dir);
            if (!directory_is_empty($this->app->project_dir) && !$force) {
                print "\ndirectory is not empty, aborting init\n" .
                    "  if you want to init anyways, use the --force flag\n";
                exit(1);
            }
            $skipped = $setup->init("minimal");
        }

        $terminal->shell_info();

        if ($skipped) {
            $terminal->shell_info("some files could not be created, because they are already there:", true);
            print_r($skipped);
        }
    }
}
