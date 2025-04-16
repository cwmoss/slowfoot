<?php

namespace slowfoot;

require_once 'template_helper.php';

class project {

    public string $base;
    public store $ds;
    public string $src;
    public array $template_helper;
    public array $pages;

    public function __construct(public configuration $config) {
        $this->src = $config->base . '/src';
        $this->base = $config->base;
    }

    public function path_prefix(): string {
        return $this->config->path_prefix;
    }

    public function dist(): string {
        return $this->config->dist; // $this->config->build['dist'];
    }

    public function info(): array {
        return $this->config->db->info;
    }

    public function info_types(): array {
        return $this->config->db->info();
    }

    public function builder(): pagebuilder {
        return new pagebuilder($this->config, $this->ds, $this->template_helper);
    }

    public function templates(): array {
        return $this->config->templates;
    }

    public function load() {
        $dataloader = $this->config->get_loader();
        $this->ds = $dataloader->load();
        $this->template_helper = load_template_helper($this->ds, $this->src, $this->config);
        $this->load_pages();
    }

    public function load_pages() {
        $pages = glob($this->src . '/pages/*.php');
        $this->pages = array_map(function ($p) {
            // pagename is everything before the first dot (can have multiple suffixes)
            return '/' . explode(".", basename($p))[0];
        }, $pages);
    }
}
