<?php

namespace slowfoot\dev;

use Psr\Http\Message\ServerRequestInterface as R;
use React\Http\Message\Response as P;
use slowfoot\project;

class preview {

    public function __construct(public project $project) {
    }

    public function __invoke(R $r): P {
        $id_type = $r->getAttribute("path", "/");
        list($id, $type) = explode('/', $id_type);
        dbg("[api/preview]", $id_type);

        $preview_obj =  []; // load_preview_object($id, $type, $config);

        #$template = $templates[$obj['_type']]['_']['template'];
        #$template = template_name($config['templates'], $obj['_type'], $name);
        #dbg('[api/preview] template', $preview_obj);
        $context = [
            'mode' => 'dev',
            'src' => $this->project->src,
            'path' => $id_type,
            'site_name' => $this->project->config->site_name ?? '',
            'site_description' => $this->project->config->site_description ?? '',
            'site_url' => $project->config->site_url ?? '',

        ];
        // TODO: migrate
        // $content = template($preview_obj['template'], ['page' => $preview_obj['data']], $template_helper, template_context('template', $context, $preview_obj, $ds, $config));
        $content = "";
        return new P(headers: [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'post-check=0, pre-check=0',
            'Pragma' => 'no-cache'
        ], body: $content);
    }
}
