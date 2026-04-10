<?php

namespace slowfoot;

use Dotenv\Dotenv;

class app {

    public string $base;
    public string $write_path = "";
    public project $project;
    public array $original_args = [];
    public function __construct(public string $project_dir, public bool $verbose, public bool $fresh) {
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
        if (file_exists("{$this->project_dir}/.env")) {
            //print "env: $base/.env";
            Dotenv::createImmutable($this->project_dir)->load();
        }

        $_ENV = array_merge(getenv(), $_ENV);
        $this->write_path = $_ENV["SLFT_WRITE_PATH"] ?? "";
    }

    public function load_project() {
        $this->project = new project(configuration::load(
            $this->project_dir,
            $this->fresh,
            write_path: $this->write_path
        ));
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
