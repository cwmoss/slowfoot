<?php

namespace slowfoot\components;

use phuety\data_container;
use phuety\phuety_context;
use phuety\asset;
use phuety\render_component;

class markdown extends render_component {

    public function render(data_container $props, array $slots, data_container $helper, phuety_context $phuety, asset $assetholder, $runner): string {
        if (isset($props->toc)) return $helper->markdown_toc($props->body);
        else return $helper->markdown($props->body);
    }
}
