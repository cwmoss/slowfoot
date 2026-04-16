<?php

namespace slowfoot\commands;

use slowfoot\util\console;
use slowfoot\context;
use slowfoot\pagebuilder;
use slowfoot\app;
use cwmoss\final_cli\cli;

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
        ?bool $f = false
    ) {

        $this->app->setup()->load_data(false);
        print memory_get_usage() . " loaded ok\n";
        $project = $this->app->project;

        if (!$project->dist()) {
            die('NO DIST-PATH FOUND');
        }

        $dist = $project->dist();
        // print PHP_SAPI . " -- " . php_sapi_name() . " -- ";
        print console::console_table(['_type' => 'type', 'total' => 'total'], $project->info_types());

        shell_info("removing old dist folder", true);
        shell_info("  => {$dist}");
        shell_exec("rm -rf $dist");
        shell_info();

        $context = new context(
            mode: 'build',
            src: $project->src,
            path: "",
            config: $project->config
        );

        shell_info("writing templates", true);

        dbg("+++ PREFIX", PATH_PREFIX);
        // print_r($project);

        $builder = $project->builder();
        $dist = $project->dist();
        $ds = $project->ds;

        foreach ($project->templates() as $type => $conf) {
            //$count = query('');
            //if($type=='article') continue;
            $bs = 100;
            $start = 0;

            // if ($type != "page") continue;
            shell_info("  => $type");

            // TODO
            foreach (query_type($project->ds, $type) as $row) {
                // if ($type == "page") var_dump($row);
                foreach ($conf as $templateconf) {
                    //	process_template_data($row, path($row['_id']));
                    $path = $ds->get_fpath($row->_id, $templateconf['name']);
                    if ($path == '/index') {
                        $path = '/';
                    }
                    if ($path == "/") {
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
                    write($content, $path, null, $dist);
                }
            }
            shell_info();
        }

        shell_info("writing pages", true);

        foreach ($project->pages as $pagename) {
            shell_info("  => $pagename");
            $pagepath = $pagename;
            if ($pagepath == '/index') {
                $pagepath = '/';
            }
            $generator = $builder->make_page_bulk($pagename, $context);
            foreach ($generator as $result) {
                write($result["content"], $pagepath, $result["pagenr"], $dist);
            }
            shell_info();
        }

        shell_info("copy assets");

        shell_exec("cp -R {$project->src}/assets {$project->dist()}/");
        shell_exec("cp -a {$project->config->var}/rendered-images/. {$project->dist()}/images");

        shell_info();

        if (isset($project->config->hooks['after_build'])) {
            shell_info("after build hook");
            $project->config->hooks['after_build']($project->config);
            shell_info();
        } else {
            shell_info("no after build hook configured", true);
        }

        // print getenv("SLFT_WRITE_PATH");
        shell_info("⚡️ done", true);
    }
}
