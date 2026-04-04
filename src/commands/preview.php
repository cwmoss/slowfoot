<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use slowfoot\app;

class preview {

    public function __construct(public app $app) {
    }

    /**
     * preview a project.
     * 
     * starts a webserver where you can preview
     * your dist/ folder
     */
    public function __invoke(
        #[cli("-d", "Set the project base directory")]
        ?string $project_directory = null,
        #[cli("-S --server server:port", "Set server and port")]
        string $server_port = "localhost:11999"
    ) {
        shell_info("starting testserver. you can review your build here.", true);
        $command = "php -S {$server_port} -t {$this->app->project->dist()}";
        print "\n";
        print "   🤟 http://{$server_port}\n\n";
        print "<cmd> click\n";
        print "have fun!\n\n";
        shell_exec($command);
    }
}
