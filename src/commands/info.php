<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use slowfoot\util\console;
use slowfoot\app;

class info {

    public function __construct(public app $app) {
        // var_dump($app);
    }
    /**
     * show some information about your project.
     * 
     */
    public function __invoke(
        #[cli("-d", "Set the project base directory")]
        ?string $project_directory = null,
        bool $webdeploy = false,
        bool $force = false
    ) {
        $boot_only_config = false;
        $boot_quiet = true;

        $this->app->setup()->load_data(true);

        // print "🌈 slowfoot\n";
        print "=> {$this->app->project_dir}\n";

        print console::console_table(['_type' => 'type', 'total' => 'total'], $this->app->project->config->db->info());
    }
}
