<?php

namespace slowfoot_plugin\phuety;

use phuety\phuety;
use slowfoot\template_contract;
use slowfoot\configuration;
use slowfoot\context;
use slowfoot\hook;
use slowfoot\components;

class phuety_adapter implements template_contract {

    public phuety $engine;

    public function __construct(public configuration $config) {
        $this->engine = new phuety($config->src, [
            'layout.*' => 'layouts/*',
            'page.*' => 'pages/*',
            'template.*' => 'templates/*',
            '*' => 'components/',
            'sft.*' => function ($tag) {
                $cls = str_replace(".", "_", $tag);
                $cls = substr($cls, 4);
                $cls = "slowfoot\\components\\$cls";
                // require_once(__DIR__ . "/../fixtures/render_components/music_index.php");
                return new $cls;
            }
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
        $cname = "template.{$name}"; // $__context->is_page ? "template.{$name}" : "template.{$name}";
        $__context->name = $cname;
        dbg("++ run template", $cname, $name, $helper["markdown"]("*yo**"));

        $helper = $this->load_late_template_helper($helper, $__context->src, $data, $__context);
        $this->engine->set_helper($helper);
        debug_js("props", $data);
        debug_js("globals", $__context);
        ob_start();
        $this->engine->run($cname, $data, $helper, $__context);
        return ob_get_clean();
    }

    public function run_page(string $_template, array $data, array $helper, context $__context): string {
        $name = ltrim($_template, "/");
        $cname = "page.{$name}"; // $__context->is_page ? "page.{$name}" : "page.{$name}";
        $__context->name = $cname;
        dbg("++ run page", $cname, $name, $__context);
        debug_js("props", $data);
        debug_js("globals", $__context);
        $this->engine->set_helper($helper);
        ob_start();
        $this->engine->run($cname, $data, $helper, $__context);
        return ob_get_clean();
    }

    public function load_late_template_helper($helper, $base, $data, ?context $context = null) {

        foreach (hook::invoke('bind_late_template_helper', [], $helper, $base, $data) as $late) {
            dbg("++ adding late helper", $late);
            $helper = array_merge($helper, $late);
        }

        return $helper;
    }
}
