<?php

namespace slowfoot;

use Dotenv\Dotenv;

class app {

    public string $base;
    public string $write_path;
    public project $project;

    public function __construct(public string $project_dir, public bool $verbose, public bool $fresh) {
        $this->base = SLOWFOOT_BASE;
        $this->init();
        $this->set_env();
        $this->load_project();
    }

    public function init() {
        if (!$this->verbose) define("SLOWFOOT_NO_DEBUG", 1);
        if (!defined('SLOWFOOT_BASE')) {
            // via php cli webserver
            #    print_r($_SERVER);
            #    print_r($_SERVER);
            // different project path without vendor/ dir?
            // TODO: better ideas
            $internal = str_replace('vendor/cwmoss/slowfoot-lib/docs_src/src', '', $_SERVER['DOCUMENT_ROOT']);
            if ($internal == $_SERVER['DOCUMENT_ROOT']) {
                unset($internal);
            }
            define('SLOWFOOT_BASE', $_SERVER['DOCUMENT_ROOT'] . '/../');
        }
        new error_handler;
        if (!defined('SLOWFOOT_PREVIEW')) {
            define('SLOWFOOT_PREVIEW', false);
        }
        if (!defined('SLOWFOOT_WEBDEPLOY')) {
            define('SLOWFOOT_WEBDEPLOY', false);
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
    }

    public function setup() {
        (new setup($this->project_dir))->setup();
        return $this;
    }

    public function setup_get() {
        return new setup($this->project_dir);
    }

    // drafts wont be loaded in build bode
    public function load_data($include_drafts = false) {
        $this->project->load($include_drafts);
        return $this;
    }
}
