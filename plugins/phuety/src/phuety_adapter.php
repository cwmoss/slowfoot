<?php

namespace slowfoot_plugin\phuety;

use phuety\phuety;
use slowfoot\template_contract;
use slowfoot\configuration;
use slowfoot\context;

class phuety_adapter implements template_contract {

    public phuety $engine;

    public function __construct(public configuration $config) {
        $this->engine = new phuety($config->src, [
            'app.layout' => 'layouts/layout',
            'app.assets' => 'assets',
            'page.*' => 'pages/*',
            'template.*' => 'templates/*',
            'doc.*' => 'components/',
            'part.*' => 'components/*'
        ], $config->src . "/compiled");
        $this->engine->set_custom_tag("page-query");
    }

    public function preprocess($_template, $_base) {
        $name = ltrim($_template, "/");
        $cname = "page.{$name}";
        $component = $this->engine->get_component($cname);
        $query = $component->custom_tags["page-query"] ?? null;
        if ($query) {
            return ["page-query" => $query["attrs"] + [
                '__content' => $query["content"],
                '__tag' => $query["name"]
            ]];
        }
        return [];
    }

    public function remove_tags($content, array $tags = []) {
        return $content;
    }

    public function run(string $_template, array $data, array $helper, context $__context): string {
        $name = $_template;
        $cname = $__context->is_page ? "template.{$name}" : "template.{$name}";
        dbg("++ run template", $cname, $name, $helper["markdown"]("*yo**"));
        $this->engine->set_helper($helper);
        ob_start();
        $this->engine->run($cname, $helper + $data);
        return ob_get_clean();
    }

    public function run_page(string $_template, array $data, array $helper, context $__context): string {
        $name = ltrim($_template, "/");
        $cname = $__context->is_page ? "page.{$name}" : "page.{$name}";
        dbg("++ run page", $cname, $name, $__context);
        $this->engine->set_helper($helper);

        ob_start();
        $this->engine->run($cname, $helper + $data);
        return ob_get_clean();
    }
}
