<?php

namespace slowfoot\image;

use Closure;

class configuration {


    public function __construct(
        public string $base = "images",
        public string $path = '/images',
        public string $src = '',
        public string $dest = 'var/rendered-images',
        public array $profiles = [],
        public bool $download = false,
        public ?Closure $map = null,
        public ?Closure $resize_cdn = null
    ) {
    }
}
