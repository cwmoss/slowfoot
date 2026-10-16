<?php

namespace slowfoot;

use Closure;

class feed {

    private Closure $mapping;

    public function __construct(public string $title = "news feed") {
    }

    public function items(Closure $mapping) {
        $this->mapping = $mapping;
    }

    public function generate(array $items = []): string {
        return "i'm the newsfeed";
    }
}
