<?php

namespace slowfoot\image;

use Closure;

class configuration {

    public string $dest;

    public function __construct(
        // TODO: leer lassen? ist verwirrend
        public string $base = "images",
        public string $path = '/images',
        public string $src = '',
        public string $var = "",
        public string $rendered = 'rendered-images',
        public array $profiles = [],
        public bool $download = false,
        public ?Closure $map = null,
        public ?Closure $resize_cdn = null
    ) {
        $this->update_paths();
    }

    public function update_paths() {
        if (!$this->var) {
            $this->var = $this->base . "/var";
        }
        $this->dest = $this->var . "/" . $this->rendered;
    }
}
