<?php

namespace slowfoot\commands;

use cwmoss\final_cli\app as final_cli;
use slowfoot\util\console;
use slowfoot\context;
use slowfoot\pagebuilder;
use slowfoot\app;
use cwmoss\final_cli\cli;
use slowfoot\terminal;

class build {

    public function __construct(public app $app) {
    }

    // build [-d <project directory>] [-f] 
    /**
     * build your project.
     * 
     * generates all pages and puts everything
     * into a dist directory
     */
    public function __invoke(
        #[cli("-d", "Set the project base directory")]
        ?string $project_directory = null,
        #[cli("-f", "fetch all contents")]
        ?bool $f = false,
        #[cli("-p --prefix", "Set the URL prefix")]
        ?string $prefix = null,
        #[cli("--html", "console output as html")]
        ?bool $html = false
    ) {
        if (final_cli::$verbose == 0) {
            ini_set("error_reporting", E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);
        }
        $this->app->setup_get()->setup();

        $terminal = new terminal;
        if ($html) $terminal->set_output("html");

        $project = $this->app->project;
        $project->load(false, $terminal);
        $terminal->println("mem: " . memory_get_usage() . " loaded ok");


        if (!$project->dist()) {
            die('NO DIST-PATH FOUND');
        }
        $prefix = $project->path_prefix();
        $terminal->println("build with prefix: <b>" . ($prefix ?: "/") . "</b>");

        $dist = $project->dist();
        // print PHP_SAPI . " -- " . php_sapi_name() . " -- ";
        $terminal->console_table(['_type' => 'type', 'total' => 'total'], $project->info_types());

        $terminal->shell_info("removing old dist folder", true);
        $terminal->shell_info("  => {$dist}");
        shell_exec("rm -rf $dist");
        $terminal->shell_info();

        $context = new context(
            mode: 'build',
            src: $project->src,
            path: "",
            config: $project->config
        );

        $terminal->shell_info("writing templates", true);

        dbg("+++ PREFIX", PATH_PREFIX);
        $builder = $project->builder();
        $dist = $project->dist();
        $ds = $project->ds;

        if ($project->config->auto_index) {
            $terminal->shell_info("auto-index", true);
            $found = $ds->find_or_select_startpage();
            // var_dump($found);
        }
        foreach ($project->templates() as $type => $conf) {
            //$count = query('');
            //if($type=='article') continue;
            $bs = 100;
            $start = 0;

            // if ($type != "page") continue;
            $terminal->shell_info("  => $type");

            // TODO
            foreach ($project->ds->query_type_batched($type) as $row) {
                // if ($type == "page") var_dump($row);
                foreach ($conf as $templateconf) {
                    //	process_template_data($row, path($row['_id']));
                    $path = $ds->get_fpath($row->_id, $templateconf['name']);
                    if ($path === '/index') {
                        $path = '/';
                    }
                    if ($path === "/") {
                        #var_dump($row);
                        #exit;
                    }
                    // print "  path: $path {$row->_id}\n";
                    if (!$path) {
                        // print "*** no path for $row->_id\n";
                        continue;
                    }
                    $context->path = $path;
                    $content = $builder->make_template(
                        $templateconf['template'],
                        $context,
                        data: $row,
                        template_conf: $templateconf
                    );
                    // empty content, means we don't want a page here
                    if ($content)
                        write($content, $path, null, $dist);
                }
            }
            $terminal->shell_info();
        }

        $terminal->shell_info("writing pages", true);

        foreach ($project->pages as $pagename) {
            $terminal->shell_info("  => $pagename");
            $pagepath = $pagename;
            if ($pagepath == '/index') {
                $pagepath = '/';
            }
            $generator = $builder->make_page_bulk($pagename, $context);
            foreach ($generator as $result) {
                if ($result["content"])
                    write($result["content"], $pagepath, $result["pagenr"], $dist);
            }
            $terminal->shell_info();
        }


        $terminal->shell_info("copy assets");
        if (is_dir("{$project->src}/assets")) {
            shell_exec("cp -R {$project->src}/assets {$project->dist()}/");
        }
        if (is_dir("{$project->config->var}/rendered-images")) {
            shell_exec("cp -a {$project->config->var}/rendered-images/. {$project->dist()}/images");
        }
        $terminal->shell_info();

        if (isset($project->config->hooks['after_build'])) {
            $terminal->shell_info("after build hook");
            $project->config->hooks['after_build']($project->config);
            $terminal->shell_info();
        } else {
            $terminal->shell_info("no after build hook configured", true);
        }

        // print getenv("SLFT_WRITE_PATH");
        $terminal->shell_info("⚡️ done", true);
    }
}
