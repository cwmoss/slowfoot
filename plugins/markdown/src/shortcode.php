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
        dbg("found shortcode?", isset($this->shortcodes[$test[0]]));
        if (!isset($this->codes[$test[0]])) return;
        // TODO: current_obj vs current_obj[page] vs nothing
        dbg("+++ object", $Excerpt["parent"]->current_obj["page"], $Excerpt["parent"]->current_obj);
        $m = $this->codes[$test[0]];
        array_shift($test);
        return $m($test, $Excerpt["parent"]->current_obj["page"], $Excerpt["parent"]->context["conf"]);
    }
}
