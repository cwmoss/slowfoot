<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use cwmoss\final_cli\terminal;
use slowfoot\util\console;
use slowfoot\store;
use slowfoot\app;


class dev {

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
        $terminal = new terminal;
        $terminal->println($this->logo);
        $src = $this->app->project_dir . "/src";
        $slft_lib_base = dirname(__DIR__);

        $devhostport = explode(':', $server_port, 2) +
            [1 => $port];

        $devserver = join(":", $devhostport);

        $this->app->setup()->load_data(true);

        // evtl. fetching data
        $project = $this->app->project;

        print console::console_table(['_type' => 'type', 'total' => 'total'], $project->ds->info());

        // this wont work :)
        // `(sleep 1 ; open http://localhost:1199/ )&`;
        // this works!
        // automatisches öffnen gefällt mir nicht mehr
        // shell_exec('(sleep 1 ; open http://localhost:1199/ ) 2>/dev/null >/dev/null &');
        $command = "XXXPHP_CLI_SERVER_WORKERS=4 php -d variables_order=EGPCS -d short_open_tag=On -S {$devserver} -t {$src} {$slft_lib_base}/_main/development.php";
        print "\n\n";

        print "starting development server\n\n";
        print "   🌈 http://$devserver\n\n";
        print "<cmd> click\n";
        print "have fun!\n\n";
        print $command . "\n";
        // $wss = "php {$slft_lib_base}/wss.php " . SLOWFOOT_BASE;
        // shell_exec("$wss &");
        // print "end";
        shell_exec($command);
        // `($command &) && ($wss &)`;
    }

    // https://www.asciiart.eu/text-to-ascii-art
    public string $logo = '
┌─┐┬  ┌─┐┬ ┬┌─┐┌─┐┌─┐┌┬┐
└─┐│  │ ││││├┤ │ ││ │ │ 
└─┘┴─┘└─┘└┴┘└  └─┘└─┘ ┴ 
 ';
}
