<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use cwmoss\final_cli\terminal;
use slowfoot\util\console;
use slowfoot\store;
use slowfoot\app;
use FrameworkX\App as xapp;
use FrameworkX\Container;
use FrameworkX\Runner\HttpServerRunner;
use slowfoot\dev\api_index;
use slowfoot\dev\error;
use slowfoot\dev\fun_and_run;
use slowfoot\dev\internal_assets_server;
use slowfoot\dev\images_server;
use slowfoot\dev\preview;
use slowfoot\dev\site;
use slowfoot\dev\timer;
use slowfoot\dev\ui_server;
use slowfoot\project;


class dev_fx {

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
        // ini_set("error_log", "php://stdout");
        terminal::println($this->logo);
        $src = $this->app->project_dir . "/src";
        $slft_lib_base = dirname(__DIR__);

        $devhostport = explode(':', $server_port, 2) +
            [1 => $port];

        $devserver = join(":", $devhostport);

        $this->app->setup()->load_data(true);

        // evtl. fetching data
        $project = $this->app->project;

        print console::console_table(['_type' => 'type', 'total' => 'total'], $project->ds->info());

        $container = new Container([
            "X_LISTEN" => $devserver,
            "X_EXPERIMENTAL_RUNNER" => HttpServerRunner::class,
            // \FrameworkX\ErrorHandler::class => fn() => new error(),
            project::class => $project
        ]);

        // dbg("make app");
        $app = new xapp($container, new error(), timer::class);
        // phpinfo();
        $this->add_routes($app, $project);
        $app->run();
    }

    public function add_routes(xapp $app, project $project): xapp {
        $images = $project->config->assets->path;

        $app->post("/__api/fetch", api_index::class);
        $app->get("/__api/index", api_index::class);
        $app->get("/__api/type/{type}[/{page}]", api_index::class);
        $app->get("/__api/id", api_index::class);
        $app->get("/__api/lolql", api_index::class);
        $app->get("/__api/fts", api_index::class);
        $app->get("/__api/preview/{path:.*}", preview::class);

        $app->post("/__fun/{fun}", fun_and_run::class);
        $app->any("/__run/{fun}", fun_and_run::class);

        $app->get("/__ui[/{path:.*}]", ui_server::class);

        $app->get("/__sf/{path:.*}", internal_assets_server::class);

        $app->get("$images/{path:.*}", images_server::class);
        $app->get("/{path:.*}", site::class);
        return $app;
    }


    // https://www.asciiart.eu/text-to-ascii-art
    public string $logo = '
┌─┐┬  ┌─┐┬ ┬┌─┐┌─┐┌─┐┌┬┐
└─┐│  │ ││││├┤ │ ││ │ │ 
└─┘┴─┘└─┘└┴┘└  └─┘└─┘ ┴ 
 ';
}
