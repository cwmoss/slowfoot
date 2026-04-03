<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;

class preview {

    /**
     * preview a project.
     * 
     * starts a webserver where you can preview
     * your dist/ folder
     */
    public function __invoke(
        #[cli("-d", "Set the project base directory")]
        ?string $project_directory = null,
        bool $webdeploy = false,
        bool $force = false
    ) {
    }
}
