<?php

namespace slowfoot\components;

use phuety\data_container;
use phuety\phuety_context;
use phuety\asset;
use phuety\render_component;
use slowfoot\favicon as fav;

class favicon extends render_component {

    public string $file_name = "";
    public string $path = "";

    // <link rel="icon" href="@assets/favicon.svg" type="image/svg+xml">
    public function link(): string {
        return sprintf('<link rel="icon" href="%s/%s" type="image/svg+xml">', $this->path, $this->file_name);
    }

    public function render(
        data_container $props,
        array $slots,
        data_container $helper,
        phuety_context $phuety,
        asset $assetholder,
        $runner
    ): string {
        $show = isset($props->show);
        if (!$show && $this->file_name) return $this->link();
        $shape = match (true) {
            isset($props->square) => "square",
            isset($props->circle) => "circle",
            isset($props->triangle) => "triangle",
            default => "circle",
        };
        $color = $props->color ?? "transparent";
        $bgcolor = $props->background ?? "transparent";
        $text = trim($slots["default"] ?? "");
        if (!$text && $props->autotext ?? null) $text = $props->globals->config->site_name;
        if ($text) $text = mb_substr($text, 0, 2);
        $textcolor = $props->textcolor ?? "white";
        $size = $props->size ?? 100;

        $f = new fav($color, $bgcolor);
        match ($shape) {
            "square" => $f->square($size),
            "triangle" => $f->triangle($size),
            default => $f->circle($size)
        };
        if ($show) return $f->xml();
        $xml = $f->xml();
        $this->file_name = sprintf("favicon-%s.svg", hash("xxh3", $xml));
        $this->path = $props->globals->config->prefix . "/assets";
        $dir = $props->globals->config->src . "/assets";
        file_put_contents($dir . "/" . $this->file_name, $xml);
        return $this->link();
    }
}
