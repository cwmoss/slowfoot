<?php

namespace slowfoot\dev;

use Psr\Http\Message\ServerRequestInterface as R;
use React\Http\Message\Response as P;
use slowfoot\pagebuilder;
use slowfoot\project;
use slowfoot\context;

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
        $project = $this->project;
        // dbg('[dev] page/template', $requestpath);
        // server::send_nocache();
        // $requestpath = '/' . $requestpath;
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
            $content = $builder->make_page($pagename, $pagenr, $requestpath, $context);
        }
        $debug = true;
        if ($debug) {
            $inspector = include_to_buffer(__DIR__ . '/../../resources/debug.php');
            $inspector_head = '<script defer src="/__sf/json-viewer.bundle.js"></script>';
            // $inspector_css = '<link rel="stylesheet" href="/__sf/inspector-json.css">';
            //$content = str_replace('</head>', $inspector_head . '</head>', $content);
            $content = str_replace('</body>', $inspector . '</body>', $content);
        }
        return $this->no_cache($content);
    }

    public function no_cache($content): P {
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
