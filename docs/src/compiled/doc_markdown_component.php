<?php
namespace compiled;

use phuety\component;
use phuety\data_container;
use phuety\phuety;
use phuety\tag;
use phuety\asset;
use phuety\phuety_context;

use function phuety\dbg;



class doc_markdown_component extends component {
    public string $uid = "doc_markdown---681a3f2375492";
    public bool $is_layout = false;
    public string $name = "doc_markdown";
    public string $tagname = "doc.markdown";
    public bool $has_template = false;
    public bool $has_code = true;
    public bool $has_style = false;
    public array $assets = array (
);
    public array $custom_tags = array (
);
    public int $total_rootelements = 0;
    public ?array $components = NULL;

    public function run_code(data_container $props, array $slots, data_container $helper, phuety_context $phuety, asset $assetholder): array{
        // dbg("++ props for component", $this->name, $props);<?php
//dbg("... all props", $props);
// $markdown = $helper["markdown"];

if (isset($props->toc)) print $helper->markdown_toc($props->body);
else print $helper->markdown($props->body);
// print $props['body'];
        return get_defined_vars();
    }

    public function render($__runner, data_container $__d, array $slots=[]):void {
        // ob_start();
        // if($this->is_layout) print '<!DOCTYPE html>';
        $__s = [];
        ?><?php // return ob_get_clean();
        // dbg("+++ assetsholder ", $this->is_start, $this->assetholder);
    }

    public function debug_info(){
        return array (
  'src' => '/Users/rw/dev/slowfoot/docs/src/components/doc_markdown.phue.php',
  'php' => 1,
);
    }
}
