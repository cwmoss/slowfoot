<?php

namespace slowfoot;

class feed_item {

    public function __construct(
        public ?string $title = null,
        public ?string $link = null,
        public ?string $description = null,
        public ?string $author = null,
        public ?string $category = null,
        public ?string $comments = null,
        public ?string $guid = null,
        public \DateTimeInterface|int|string|null $pubDate = null
    ) {
    }

    public function to_array(): array {
        return array_filter(get_object_vars($this), static fn($value) => $value !== null);
    }
}
