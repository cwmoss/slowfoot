<?php

namespace slowfoot;

use DateTimeZone;
use OutOfRangeException;
use slowfoot\store;
use slowfoot\store\memory;
use slowfoot\store\sqlite;
use slowfoot\loader;
use slowfoot\template;
use slowfoot\image;
use slowfoot_plugin\phuety\phuety_adapter;

use function lolql\parse;
use function lolql\query as lquery;

/*

https://github.com/paquettg/php-html-parser
https://github.com/Masterminds/html5-php

if (! function_exists(__NAMESPACE__ . '\greetings'))

'site_name' => 'mumok Demo',
    'site_description' => 'look at beautiful works of art',
    'site_url' => '',
    // TODO: solve genenv vs ENV problem
    'path_prefix' => getenv('PATH_PREFIX') ?: $_ENV['PATH_PREFIX'] ?: '',
    'title_template' => '',
*/

class configuration {

    static public string $config_filename = "slowfoot-config.php";

    public string $base;
    public string $var;
    public string $dist;
    public DateTimeZone $tz;
    public ?store $db;

    public function __construct(
        public string $site_name = "",
        public string $site_url = "",
        public string $site_description = "",
        public string $path_prefix = "",
        public string $title_template = "",
        public array $sources = [],
        public array $templates = [],
        public string|array $store = 'sqlite',
        public array $hooks = [],
        public ?image\configuration $assets = null,
        public array $plugins = [],
        public array $preview = [],
        public string|array $build = ['dist' => 'dist'],
        public bool $is_prod = false,
        public string $timezone = "Europe/Berlin",
        public string|template_contract $template_engine = phuety_adapter::class,
        public string $src = "src/",
        public bool $auto_index = false
    ) {
        $this->tz = new DateTimeZone($timezone);
        date_default_timezone_set($timezone);
    }

    static function load(
        string $dir,
        bool $fresh_fetch = false,
        ?configuration $conf = null,
        bool $is_prod = false,
        string $write_path = "",
        ?string $prefix = null
    ): self {
        // TODO: run without config
        if (!$conf) $conf = require($dir . "/" . self::$config_filename);
        $conf->is_prod = $is_prod;
        $conf->base = '/' . get_absolute_path($dir);
        $conf->src = $conf->base . "/" . $conf->src;
        if ($write_path) {
            $conf->dist = $conf->base . "/" . $write_path . "/dist";
            $conf->var = $conf->base .  "/" . $write_path . "/var";
        } else {
            $conf->dist = $conf->base . "/dist";
            $conf->var = $conf->base . "/var";
        }
        // override prefix with cli option
        if (!is_null($prefix)) $conf->path_prefix = $prefix;
        $conf->init($fresh_fetch);
        return $conf;
    }

    public function init(bool $fresh_fetch) {
        if (!is_dir($this->var)) {
            mkdir($this->var);
        }

        foreach ($this->templates as $name => $t) {
            $this->templates[$name] = $this->normalize_template_config($name, $t);
        }

        $this->store = $this->normalize_store_config();
        $this->db = $this->get_store($fresh_fetch);

        $this->assets = $this->normalize_assets_config($this->assets);
        $this->init_plugins();

        $this->build = $this->normalize_build_config($this->build);
    }

    public function fresh_store() {
        $this->db = null;
        $this->db = $this->get_store(true);
    }

    public function get_loader(): loader {
        return new loader($this);
    }
    public function get_store(bool $fresh_fetch = false): store {
        if (isset($this->db)) return $this->db;
        if (strpos($this->store['adapter'], 'sqlite') === 0) {
            $db = new sqlite($this->store, $fresh_fetch);
        } else {
            $db = new memory();
        }
        return new store($db, $this->templates);
    }
    public function set_template_engine(template_contract $tpl) {
        $this->template_engine = $tpl;
        return $this;
    }
    public function get_template_engine(): template_contract {
        if (is_string($this->template_engine))
            $this->template_engine = new ($this->template_engine)($this);
        return $this->template_engine;
    }

    public function get_image_processor() {
        $processor = new image\processor($this->assets);
        return $processor;
    }
    // TODO:
    //  require in global context?
    //  do wee need a plugin init /w pconf?
    //  plugin via composer?
    //  raise error?
    public function init_plugins() {
        foreach ($this->plugins as $plugin) {
            if (method_exists($plugin, "init")) $plugin->init();
        }
    }

    public function get_plugin(object $class) {
        foreach ($this->plugins as $plugin) {
            if ($class == get_class($plugin)) return $plugin;
        }
        throw new OutOfRangeException("plugin $class not found");
    }

    public function normalize_template_config(string $name, string|array $config) {
        if (!is_array($config) || is_assoc($config)) {
            $config = [$config];
        }
        $tpl = [];
        foreach ($config as $t) {
            if (!is_array($t)) {
                $t = ['path' => $t];
            }
            if (is_string($t['path'])) {
                $t['path'] = new path($t['path'])->make_function();
            }
            $subname = $t['name'] ?? '_';
            $tpl[$subname] = array_merge(['type' => $name, 'template' => $name, 'name' => $subname], $t);
        }
        return $tpl;
    }

    function normalize_store_config() {
        $store = ['adapter' => $this->store];
        $store['base'] = $this->var;
        return $store;
    }

    function normalize_assets_config(?image\configuration $assets): image\configuration {
        if (!$assets) {
            $assets = new image\configuration($this->base, var: $this->var);
            return $assets;
        }
        $assets->base = $this->base;
        $assets->var = $this->var;
        $assets->update_paths();

        if (!$assets->map) {
            $assets->map = function ($img) {
                return hook::invoke_filter('assets_map', $img, $this->db);
            };
        }
        return $assets;
    }
    function normalize_build_config(string|array $build): array {
        if (is_string($build)) {
            $build = ['dist' => $build];
        }
        #if($build['dist'][0]!='/'){
        $build['dist'] = $this->base . '/' . $build['dist'];
        #}
        return $build;
    }
}
