<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use cwmoss\final_cli\terminal;
use slowfoot\util\console;
use slowfoot\store;
use slowfoot\app;
use Workerman\Worker;

class wmdev {

    public function __construct(public app $app) {
    }
    // dev [-S <server:port>] [-P <port>] [-f] [-d <project directory>]
    /**
     * start the dev server.
     * 
     * with the dev server you can easily watch
     * your changes as you are working on templates
     */
    public function __invoke(
        #[cli("-d", "Set the project base directory")]
        ?string $project_directory = null,

        #[cli("-S --server server:port", "Set server and port")]
        string $server_port = "0.0.0.0:1199",

        #[cli("-p --port port", "Set port only")]
        int $port = 1199,

        #[cli("-f", "fetch all contents")]
        ?bool $f = false
    ) {

        terminal::println($this->logo);


        $devhostport = explode(':', $server_port, 2) +
            [1 => $port];

        $devserver = join(":", $devhostport);

        $this->app->setup()->load_data(true);

        // evtl. fetching data
        $project = $this->app->project;

        print console::console_table(['_type' => 'type', 'total' => 'total'], $project->ds->info());
        print "\n\n";

        print "starting development server\n\n";
        print "   🌈 http://$devserver\n\n";
        print "<cmd> click\n";
        print "have fun!\n\n";

        $http_worker = new Worker("http://$devserver");

        // 4 processes
        $http_worker->count = 4;

        // Emitted when data received
        $http_worker->onMessage = function ($connection, $request) {
            //var_dump($request);
            //$request->get();
            //$request->post();
            //$request->header();
            //$request->cookie();
            //$request->session();
            var_dump($request->uri());
            //$request->path();
            //$request->method();
            header('x-server-name: workerman');
            // Send data to client
            $connection->send("Hello World");
        };

        // Run all workers
        Worker::runAll();
    }

    // https://www.asciiart.eu/text-to-ascii-art
    public string $logo = '
┌─┐┬  ┌─┐┬ ┬┌─┐┌─┐┌─┐┌┬┐
└─┐│  │ ││││├┤ │ ││ │ │ 
└─┘┴─┘└─┘└┴┘└  └─┘└─┘ ┴ 
 ';
}
