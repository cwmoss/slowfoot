<?php
namespace compiled;

use phuety\component;
use phuety\data_container;
use phuety\phuety;
use phuety\tag;
use phuety\asset;
use phuety\phuety_context;

use function phuety\dbg;



use slowfoot\feed;


/**
 * /Users/rw/dev/slowfoot/docs/src//pages/feed.phue.php ~ 6
 */

class page_feed_component extends component {
    public string $uid = "page_feed---d3f6ae";
    public bool $is_layout = false;
    public string $name = "page_feed";
    public string $tagname = "page.feed";
    public bool $has_template = true;
    public bool $has_code = true;
    public bool $has_style = false;
    public array $assets = array (
);
    public array $custom_tags = array (
  'page-query' => 
  array (
    'name' => 'page-query',
    'attrs' => 
    array (
    ),
    'content' => '
    *(_type=="chapter")
    order(_file.modified desc)
    limit(10)
',
  ),
);
    public int $total_rootelements = 0;
    public ?array $components = NULL;

    public function run_code(data_container $props, array $slots, data_container $helper, phuety_context $phuety, asset $assetholder): ?array{
        // dbg("++ props for component", $this->name, $props);
$feed = new feed("slowfoot documentation", "https://cwmoss.github.io/");
$feed->items(function ($it) use ($helper) {
    return [
        "title" => $it->title,
        "pubDate" => $it->_file->modified,
        "link" => $helper->path($it)
    ];
});

print $feed->generate($props->page);

// var_dump($props->page);

        return get_defined_vars();
    }

    public function render($__runner, data_container $__d, array $slots=[]):void {
        // ob_start();
        // if($this->is_layout) print '<!DOCTYPE html>';
        $__s = [];
        ?><?php // return ob_get_clean();
        // dbg("+++ assetsholder ", $this->is_start, $this->assetholder);
    }

    // public function debug_info(){
    //    return /Users/rw/dev/slowfoot/docs/src//pages/feed.phue.php ~ 6;
    // }
}
