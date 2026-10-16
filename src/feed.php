<?php

namespace slowfoot;

use Closure;

class feed {

    private Closure $mapping;

    public function __construct(
        public string $title = "news feed",
        public string $link = "",
        public string $description = ""
    ) {
    }

    public function items(Closure $mapping) {
        $this->mapping = $mapping;
    }

    public function generate(array $items = []): string {
        $mapped_items = array_map($this->mapping, $items);
        $link = $this->link;
        if ($link === "" && isset($mapped_items[0]["link"])) {
            $link = (string) $mapped_items[0]["link"];
        }
        $description = $this->description !== "" ? $this->description : $this->title;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0"><channel>';
        $xml .= $this->element("title", $this->title);
        if ($link !== "") {
            $xml .= $this->element("link", $link);
        }
        $xml .= $this->element("description", $description);

        foreach ($mapped_items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $xml .= '<item>';
            foreach (["title", "link", "description", "author", "category", "comments", "guid", "pubDate"] as $field) {
                if (isset($item[$field])) {
                    $value = $field === "pubDate" ? $this->format_pub_date($item[$field]) : (string) $item[$field];
                    if ($value !== null) {
                        $xml .= $this->element($field, $value);
                    }
                }
            }
            $xml .= '</item>';
        }

        return $xml . '</channel></rss>';
    }

    private function element(string $name, string $value): string {
        return sprintf('<%1$s>%2$s</%1$s>', $name, htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
    }

    private function format_pub_date(mixed $value): ?string {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_RSS);
        }

        $timestamp = is_numeric($value) ? (int) $value : strtotime((string) $value);
        return $timestamp === false ? null : gmdate(DATE_RSS, $timestamp);
    }
}
