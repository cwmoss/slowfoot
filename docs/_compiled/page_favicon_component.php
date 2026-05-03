<?php
namespace compiled;

use phuety\component;
use phuety\data_container;
use phuety\phuety;
use phuety\tag;
use phuety\asset;
use phuety\phuety_context;

use function phuety\dbg;



/**
 * /Users/rw/dev/slowfoot/docs/src//pages/favicon.phue.php ~ 
 */

class page_favicon_component extends component {
    public string $uid = "page_favicon---f59877";
    public bool $is_layout = false;
    public string $name = "page_favicon";
    public string $tagname = "page.favicon";
    public bool $has_template = true;
    public bool $has_code = false;
    public bool $has_style = false;
    public array $assets = array (
  0 => 
  array (
    0 => 'style',
    1 => 'head',
    2 => 
    array (
    ),
    3 => '<style>
    div.favicons {
        display: flex;
        flex-wrap: auto;
        gap: 2rem;

        div {
            width: 32px;
            height: 32px;
        }
    }
</style>',
  ),
);
    public array $custom_tags = array (
);
    public int $total_rootelements = 1;
    public ?array $components = array (
  0 => 'layout.default',
  1 => 'sft.favicon',
);

    public function run_code(data_container $props, array $slots, data_container $helper, phuety_context $phuety, asset $assetholder): ?array{
        // dbg("++ props for component", $this->name, $props);
        return get_defined_vars();
    }

    public function render($__runner, data_container $__d, array $slots=[]):void {
        // ob_start();
        // if($this->is_layout) print '<!DOCTYPE html>';
        $__s = [];
        ?><?php array_unshift($__s, []); ob_start(); ?>
    <article>
        <div class="favicons">
            <div>
                <?php $__runner($__runner, "sft.favicon", $__d->_get("phuety")->with($this->tagname, "sft.favicon"), [] + array (
  'show' => '',
  'square' => '',
  'color' => 'yellow',
) ); ?>
            </div>
            <div>
                <?php $__runner($__runner, "sft.favicon", $__d->_get("phuety")->with($this->tagname, "sft.favicon"), [] + array (
  'show' => '',
  'circle' => '',
  'color' => 'blue',
) ); ?>
            </div>
            <div>
                <?php $__runner($__runner, "sft.favicon", $__d->_get("phuety")->with($this->tagname, "sft.favicon"), [] + array (
  'show' => '',
  'circle' => '',
  'background' => 'black',
  'color' => 'red',
  'size' => '50',
) ); ?>
            </div>
            <div>
                <?php $__runner($__runner, "sft.favicon", $__d->_get("phuety")->with($this->tagname, "sft.favicon"), [] + array (
  'show' => '',
  'circle' => '',
  'background' => 'black',
  'color' => 'green',
  'size' => '20',
) ); ?>
            </div>
            <div>
                <?php array_unshift($__s, []); ob_start(); ?>t<?php $__runner($__runner, "sft.favicon", $__d->_get("phuety")->with($this->tagname, "sft.favicon"), [] + array (
  'show' => '',
  'square' => '',
  'color' => 'yellow',
) , ["default" => ob_get_clean()]+array_shift($__s)); ?>
            </div>
            <div>
                <?php array_unshift($__s, []); ob_start(); ?>sf<?php $__runner($__runner, "sft.favicon", $__d->_get("phuety")->with($this->tagname, "sft.favicon"), [] + array (
  'show' => '',
  'circle' => '',
  'color' => 'blue',
) , ["default" => ob_get_clean()]+array_shift($__s)); ?>
            </div>

        </div>
    </article>
<?php $__runner($__runner, "layout.default", $__d->_get("phuety")->with($this->tagname, "layout.default"), ["page"=> $__d->_get("page")] + array (
  'title' => 'favicon test',
) , ["default" => ob_get_clean()]+array_shift($__s)); ?>

<?php // return ob_get_clean();
        // dbg("+++ assetsholder ", $this->is_start, $this->assetholder);
    }

    // public function debug_info(){
    //    return /Users/rw/dev/slowfoot/docs/src//pages/favicon.phue.php ~ ;
    // }
}
