<?php

namespace slowfoot;

use DOMDocument;

class favicon {

    public DOMDocument $svg;
    public int $base = 128;

    public function __construct(public string $color = "yellow", public string $background = "") {
        $this->init();
    }

    public function init() {
        $this->svg = new DOMDocument();
        $svg = $this->svg->createElement("svg");
        // xmlns='http://www.w3.org/2000/svg'
        $svg->setAttribute("xmlns", "http://www.w3.org/2000/svg");
        $svg->setAttribute("width", "100%");
        $svg->setAttribute("height", "100%");
        $svg->setAttribute("viewBox", "0 0 $this->base $this->base");
        $this->svg->appendChild($svg);
    }

    public function square(int $size = 100) {
        $width = (int) $size * (128 / 100.0);
        $offset = (int) (($this->base - $width) / 2.0);
        $obj = $this->svg->createElement("rect");
        $obj->setAttribute("x", $offset);
        $obj->setAttribute("y", $offset);
        $obj->setAttribute("width", $width);
        $obj->setAttribute("height", $width);
        $obj->setAttribute("style", "fill: $this->color");
        $this->svg->documentElement->appendChild($obj);
    }

    public function circle(int $size = 100) {
        $radius = (int) ($size * (128 / 100.0)) / 2.0;
        $center = (int) $this->base / 2.0;
        $obj = $this->svg->createElement("circle");
        $obj->setAttribute("cx", $center);
        $obj->setAttribute("cy", $center);
        $obj->setAttribute("r", $radius);
        $obj->setAttribute("style", "fill: $this->color");
        $this->svg->documentElement->appendChild($obj);
    }

    public function xml() {
        return $this->svg->saveXML($this->svg->documentElement, LIBXML_NOEMPTYTAG);
    }
}
