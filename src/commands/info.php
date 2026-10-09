<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use slowfoot\util\console;
use slowfoot\app;
use slowfoot\terminal;

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
        bool $system = false
    ) {
        $terminal = new terminal;
        if ($system) {
            $terminal->println("systen info");
            $terminal->println("php version: " . phpversion());
            $terminal->println("php moduls: " . join(",", get_loaded_extensions()));

            // phpinfo(INFO_MODULES);
            if ($this->app->verbose) {
                phpinfo();

                var_dump(gd_info());
            }
            return;
        }

        $this->app->setup()->load_data(true);

        // print "🌈 slowfoot\n";
        print "=> {$this->app->project_dir}\n";

        print console::console_table(['_type' => 'type', 'total' => 'total'], $this->app->project->config->db->info());
    }
}
