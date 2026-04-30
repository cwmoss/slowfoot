<?php

namespace slowfoot;

use slowfoot_plugin\phuety\phuety_adapter;
use slowfoot_plugin\markdown;

class app {

    public string $base;
    public string $write_path = "";
    public project $project;
    public array $original_args = [];

    public function __construct(
        public string $project_dir,
        public bool $verbose,
        public bool $fresh,
        public ?string $prefix = null
    ) {
        $this->base = $project_dir;
        $this->init();
        $this->set_env();
    }

    public function init() {
        if (!$this->verbose) define("SLOWFOOT_NO_DEBUG", 1);
        new error_handler;
        if (!defined('SLOWFOOT_PREVIEW')) {
            define('SLOWFOOT_PREVIEW', false);
        }
    }

    public function set_env() {
        new dotenv($this->project_dir)->load();

        $_ENV = array_merge(getenv(), $_ENV);
        $this->write_path = $_ENV["SLFT_WRITE_PATH"] ?? "";
    }

    public function load_project() {
        $conf = null;
        if (!file_exists($this->project_dir . "/" . configuration::$config_filename)) {
            $conf = new configuration(
                src: "",
                sources: [
                    "md" => new markdown\loader('*.md'),
                ],
                templates: ["md" => "/:_id"],
                plugins: [
                    new markdown\markdown_plugin()
                ],
                template_engine: new phuety_adapter(null, [
                    "*" => __DIR__ . "/../resources/default_templates/"
                ], $this->project_dir, ""),
                auto_index: true
            );
            // $tpl = ;
            // $conf->set_template_engine($tpl);
            // print_r($conf);
        }
        // print "load project\n";
        // print_r($conf);
        $this->project = new project(configuration::load(
            $this->project_dir,
            $this->fresh,
            $conf,
            write_path: $this->write_path,
            prefix: $this->prefix
        ));
        // TODO: remove const, don't differenciate
        if (!defined('PATH_PREFIX')) {
            if (PHP_SAPI == 'cli-server') {
                define('PATH_PREFIX', "");
            } else {
                define('PATH_PREFIX', $this->project->path_prefix());
            }
        }
        return $this;
    }

    public function setup() {
        (new setup($this->project_dir))->setup();
        return $this;
    }

    public function setup_get() {
        return new setup($this->project_dir, $this->write_path);
    }

    // drafts wont be loaded in build bode
    public function load_data($include_drafts = false) {
        $this->project->load($include_drafts);
        return $this;
    }
}
