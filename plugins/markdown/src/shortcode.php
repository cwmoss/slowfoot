<?php

namespace slowfoot_plugin\markdown;

class shortcode {

    public function __construct(public array $codes = []) {
    }

    public function resolve($Excerpt, configuration $conf, $page) {
        /*dbg(
            "+++ shortcode excerpt +++",
            get_class($Excerpt["parent"]->context["conf"]),
            $Excerpt["parent"]->current_obj,
            $this->codes
        );*/
        $test = explode(" ", trim($Excerpt['text'], " []"));
        $shortcode = $test[0];
        dbg("found shortcode?", isset($this->shortcodes[$shortcode]));
        if (!isset($this->codes[$shortcode])) return;
        // TODO: current_obj vs current_obj[page] vs nothing
        dbg("+++ shortcode object", $shortcode, $page);
        $m = $this->codes[$shortcode];
        array_shift($test);
        return [$shortcode, $m($test, $page, $conf)];
    }
}
