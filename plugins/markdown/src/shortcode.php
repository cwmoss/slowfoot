<?php

namespace slowfoot_plugin\markdown;

class shortcode {

    public function __construct(public array $codes = []) {
    }

    public function resolve($Excerpt) {
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
        dbg("+++ object", $Excerpt["parent"]->current_obj["page"], $Excerpt["parent"]->current_obj);
        $m = $this->codes[$shortcode];
        array_shift($test);
        return [$shortcode, $m($test, $Excerpt["parent"]->current_obj["page"], $Excerpt["parent"]->context["conf"])];
    }
}
