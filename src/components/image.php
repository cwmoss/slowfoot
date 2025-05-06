<?php

namespace slowfoot\components;

use phuety\data_container;
use phuety\phuety_context;
use phuety\asset;
use phuety\render_component;

class image extends render_component {

    public function render(data_container $props, array $slots, data_container $helper, phuety_context $phuety, asset $assetholder, $runner): string {
        return $helper->image_tag($props->src, $props->size, ['alt' => $props->alt ?? ""]);
    }
}
