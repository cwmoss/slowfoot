<?php

namespace slowfoot\dev;

use Psr\Http\Message\ServerRequestInterface as R;
use React\Http\Message\Response as P;
use slowfoot\pagebuilder;
use slowfoot\project;
use slowfoot\context;
use Symfony\Component\Process\Process;

class site {

    public function __construct(public project $project, public assets_server $assets) {
    }

    public function __invoke(R $r): P {
        $requestpath = $r->getUri()->getPath();
        dbg("+++ site: ", $requestpath);
        // asset files should be handled via assets_server
        // TODO: make rules more tight (file_exists or allowed list of basepaths)
        if (preg_match("!\.\w{1,5}$!", $requestpath)) {
            return ($this->assets)($r);
        }
        // when running in worker/deamon mode we'll create the page
        // via a subprocess
        // atm this is only with micro sapi not frankenphp, cli-server, ...
        $sapi = php_sapi_name();
        dbg("site page preview sapi", $sapi);
        if ($sapi == "micro") {
            $content = $this->generate_content_in_child_process($requestpath);
        } else {
            $content = self::generate_content($this->project, $requestpath);
        }
        return $this->no_cache($content);
    }

    public function generate_content_in_child_process(string $requestpath): string {
        // Determine the location of the running binary dynamically
        // TODO: would not work in phar
        $binaryPath = $_SERVER["_"];
        $params = [$binaryPath, "show", "-d={$this->project->base}"];
        $pfx = $this->project->path_prefix();
        if ($pfx) $params[] = "-p={$pfx}";
        $params[] = $requestpath;
        // Spawn a completely fresh, isolated instance of this exact binary
        // We pass the '__worker' flag to trigger the sandbox environment above
        $process = new Process($params);

        // Pass request variables safely via STDIN to the isolated worker
        //$process->setInput(json_encode([
        //    'path' => $request->getUri()->getPath()
        //]));

        $process->run(); // Executes synchronously for this specific web request

        if (!$process->isSuccessful()) {
            // return React\Http\Message\Response::plaintext("Worker Crash: " . $process->getErrorOutput())->withStatus(500);
        }

        // $response = json_decode($process->getOutput(), true);
        return $process->getOutput();
    }

    public static function generate_content(project $project, string $requestpath): string {
        // startseite?
        if ($requestpath == '/' || $requestpath == '') {
            $requestpath = '/index';
        }
        $builder = new pagebuilder($project->config, $project->ds, $project->template_helper);
        #dbg("dev: req", $requestpath);
        [$obj_id, $name] = $project->ds->get_by_path($requestpath);
        // dbg("dev ID - name", $obj_id, $name, $requestpath);
        $context = new context(
            mode: 'dev',
            src: $project->src,
            path: $requestpath,
            config: $project->config
        );


        if ($obj_id) {
            $content = $builder->make_template($name, $context, $obj_id);
        } else {
            list($dummy, $pagename, $pagenr) = explode('/', $requestpath) + [2 => 0];
            $pagename = '/' . $pagename;
            if ($pagename == '/') {
                //    $pagename='/index';
                // $pagename = "/index";
            }

            // dbg('page...', $pagename, $pagenr, $requestpath, $project->pages);
            $obj_id = array_search($pagename, $project->pages);
            $content = $builder->make_page($pagename, (int) $pagenr, $requestpath, $context);
        }
        $debug = true;
        if ($debug) {
            $inspector = include_to_buffer(__DIR__ . '/../../resources/debug.php');
            // $inspector_head = '<script defer src="/__sf/json-viewer.bundle.js"></script>';
            // $inspector_css = '<link rel="stylesheet" href="/__sf/inspector-json.css">';
            //$content = str_replace('</head>', $inspector_head . '</head>', $content);
            $content = str_replace('</body>', $inspector . '</body>', $content);
        }
        return $content;
    }

    public function no_cache(string $content): P {
        return new P(headers: [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'post-check=0, pre-check=0',
            'Pragma' => 'no-cache'
        ], body: $content);
    }
    /*
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: text/html');
        */
}
