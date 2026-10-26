<?php

namespace slowfoot\components;

use phuety\data_container;
use phuety\phuety_context;
use phuety\asset;
use phuety\render_component;

class markdown extends render_component {

    public function render(data_container $props, array $slots, data_container $helper, phuety_context $phuety, asset $assetholder, $runner): string {
        if (!$props->body) return "";
        // dbg("helper....", $helper);
        $parser = $helper->markdown_parser();
        if ($props->mdcontext) {
            $parser->set_current_obj(["page" => $props->mdcontext]);
        }
        // only TOC?
        if (isset($props->toc)) {
            $parser->body($props->body);
            return $parser->contentsList();
            // return $helper->markdown_toc($props->body);
        }
        return $parser->text($props->body);
    }
}
