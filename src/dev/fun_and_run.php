<?php

namespace slowfoot\dev;

use Psr\Http\Message\ServerRequestInterface as R;
use React\Http\Message\Response as P;
use slowfoot\project;
use wrun\runner;

/*
$router->post('/__fun/(.*)', function ($requestpath) {
    $docbase = $_SERVER['DOCUMENT_ROOT'] . '/../endpoints';
    include($docbase . "/" . $requestpath);
    exit;
});

$router->all('/__run/(.*)', function ($requestpath) {
    $funbase = $_SERVER['DOCUMENT_ROOT'] . '/../server-functions';
    $runner = new runner($funbase);
    $runner->run($requestpath);
});
*/

class fun_and_run {

    public string $docbase;
    public string $funbase;

    public function __construct(public project $project, public assets_server $assets) {
        $this->docbase = $project->base . "/endpoints";
        $this->funbase = $project->base . "/server-functions";
    }

    public function __invoke(R $r): P {
        $requestpath = explode("/", $r->getUri()->getPath());
        if ($requestpath[1] == "__fun") {
            return $this->fun($requestpath[2]);
        }
        $runner = new runner($this->funbase);
        $resp = $runner->run($requestpath[2]);
        return new P(
            $resp->status,
            ["Content-Type" => $resp->content_type],
            json_encode($resp->body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    public function fun(string $path): P {
        ob_start();
        include($this->docbase . "/" . $path);
        $output = ob_get_clean();
        return new P(
            200,
            ["Content-Type" => "application/json"],
            $output
        );
    }
}
