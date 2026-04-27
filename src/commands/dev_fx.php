<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use slowfoot\terminal;
use slowfoot\util\console;
use slowfoot\store;
use slowfoot\app;
use FrameworkX\App as xapp;
use FrameworkX\Container;
// use FrameworkX\Runner\HttpServerRunner;
use slowfoot\dev\HttpServerRunner;

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

    /**
     * start the php-cli webserver as dev server.
     * 
     * with the dev server you can easily watch
     * your changes as you are working on templates
     */
    public function cli_server(
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

        $devhostport = explode(':', $server_port, 2) +
            [1 => $port];

        $devserver = join(":", $devhostport);

        $this->app->setup()->load_data(true);

        // evtl. fetching data
        $project = $this->app->project;

        // $src = $project->src;
        $slft_lib_base = dirname(__DIR__);

        if ($project->config->auto_index) {
            $terminal->shell_info("auto-index", true);
            $found = $project->ds->find_or_select_startpage();
            // var_dump($found);
        }

        print console::console_table(['_type' => 'type', 'total' => 'total'], $project->ds->info());
        // XXXPHP_CLI_SERVER_WORKERS=4 
        $command = "php -d variables_order=EGPCS -d short_open_tag=On -S {$devserver} -t {$project->base} {$slft_lib_base}/dev/cli_server.php";
        print "\n\n";

        print "starting development server\n\n";
        print "   🌈 http://$devserver\n\n";
        print "have fun!\n\n";
        print $command . "\n";
        shell_exec($command);
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
        $terminal = new terminal;
        $terminal->println($this->logo);

        $devhostport = explode(':', $server_port, 2) +
            [1 => $port];

        $devserver = join(":", $devhostport);

        $this->app->setup()->load_data(true);

        // evtl. fetching data
        $project = $this->app->project;

        print console::console_table(['_type' => 'type', 'total' => 'total'], $project->ds->info());
        $_ENV["X_LISTEN"] = $devserver;

        $container = new Container([
            "X_LISTEN" => $devserver,
            "X_EXPERIMENTAL_RUNNER" => HttpServerRunner::class,
            // \FrameworkX\ErrorHandler::class => fn() => new error(),
            project::class => $project
        ]);

        $restart = false;
        // dbg("make app");
        $app = new xapp($container, new error(), timer::class);
        // phpinfo();
        $app->get("/__restart", function () use ($container, &$restart) {
            // $app->
            dbg("+++ restart");
            $container->getRunner()->stop();
            $restart = true;
            // exit;
        });

        self::add_routes($app, $project);
        $app->run();
        // var_dump($restart);
        // var_dump($this->app->original_args);

        // $cmd = join(" ", $this->app->original_args);
        // print "$cmd \n";
        // exec("$cmd > /dev/null &");
        // $this->execInBackground($this->app->original_args);
    }

    static public function add_routes(xapp $app, project $project): xapp {
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

    public function execInBackground($cmd) {
        $cmd = join(" ", $cmd);
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B " . $cmd, "r"));
        } else {
            exec($cmd . " &");
        }
    }

    // https://www.asciiart.eu/text-to-ascii-art
    public string $logo = '
┌─┐┬  ┌─┐┬ ┬┌─┐┌─┐┌─┐┌┬┐
└─┐│  │ ││││├┤ │ ││ │ │ 
└─┘┴─┘└─┘└┴┘└  └─┘└─┘ ┴ 
 ';
}

/*
if ($hr) {
    require_once __DIR__ . '/hot-reload/HotReloader.php';
    $htrldr = new HotReloader\HotReloader('//localhost:1199/phrwatcher.php');
    $js = $htrldr->init();
    $content = str_replace('</html>', $js . '</html>', $content);
}
*/
