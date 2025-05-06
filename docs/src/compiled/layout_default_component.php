<?php
namespace compiled;

use phuety\component;
use phuety\data_container;
use phuety\phuety;
use phuety\tag;
use phuety\asset;
use phuety\phuety_context;

use function phuety\dbg;



class layout_default_component extends component {
    public string $uid = "layout_default---681a40984a98c";
    public bool $is_layout = true;
    public string $name = "layout_default";
    public string $tagname = "layout.default";
    public bool $has_template = true;
    public bool $has_code = true;
    public bool $has_style = false;
    public array $assets = array (
);
    public array $custom_tags = array (
);
    public int $total_rootelements = 2;
    public ?array $components = array (
  0 => 'top.nav',
);

    public function run_code(data_container $props, array $slots, data_container $helper, phuety_context $phuety, asset $assetholder): array{
        // dbg("++ props for component", $this->name, $props);
$title = "HUH"; // $__context->config->site_name;


        return get_defined_vars();
    }

    public function render($__runner, data_container $__d, array $slots=[]):void {
        // ob_start();
        // if($this->is_layout) print '<!DOCTYPE html>';
        $__s = [];
        ?><!DOCTYPE html><html><head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <?= tag::tag_open_merged_attrs("link", ["href"=> $__d->_call("path_asset")("/gfx/favicon-96x96.png")], array (
  'rel' => 'SHORTCUT ICON',
  'type' => 'image/png',
) ) ?>

    <?= tag::tag_open_merged_attrs("link", ["href"=> $__d->_call("path_asset")("/css/prism.css", true)], array (
  'rel' => 'stylesheet',
  'type' => 'text/css',
) ) ?>
    <?= tag::tag_open_merged_attrs("link", ["href"=> $__d->_call("path_asset")("/css/app.css", true)], array (
  'rel' => 'stylesheet',
  'type' => 'text/css',
) ) ?>

    <?= tag::tag_open_merged_attrs("script", ["src"=> $__d->_call("path_asset")("/js/app.js")], array (
) ) ?></script>
    <title><?= tag::h($__d->_get("title")) ?></title>



</head><body>

    <header>
        <div class="logo">slowfoot Docs <a href="https://github.com/cwmoss/slowfoot">github</a></div>
    </header>



    <main>

        <?php $__runner($__runner, "top.nav", $__d->_get("phuety")->with($this->tagname, "top.nav"), ["current_id"=> $__d->_get("page")->_id, "current"=> (($__d->_get("page")->_file) ? ($__d->_get("page")->_file) : ([]))] + array (
) ); ?>

        <?=$slots["default"]??""?>

    </main>

    <footer>
        <div class="content">
            © 2025
        </div>
    </footer>
    <?= tag::tag_open_merged_attrs("script", ["src"=> $__d->_call("path_asset")("/js/prism.js")], array (
) ) ?></script>


</body></html><?php // return ob_get_clean();
        // dbg("+++ assetsholder ", $this->is_start, $this->assetholder);
    }

    public function debug_info(){
        return array (
  'src' => '/Users/rw/dev/slowfoot/docs/src/layouts/default.phue.php',
  'php' => 42,
);
    }
}
