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
        $mapped_items = array_map(static function ($item): array {
            if ($item instanceof feed_item) {
                return $item->to_array();
            }
            return is_array($item) ? $item : [];
        }, $mapped_items);
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
            $xml .= '<item>';
            foreach (["title", "link", "description", "author", "category", "comments", "guid", "pubDate"] as $field) {
                if (isset($item[$field])) {
                    $value = match ($field) {
                        "pubDate" => $this->format_pub_date($item[$field]),
                        "link" => $this->absolute_item_link((string) $item[$field]),
                        default => (string) $item[$field],
                    };
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

    private function absolute_item_link(string $link): string {
        $item_url = parse_url($link);
        if (isset($item_url["scheme"], $item_url["host"])) {
            return $link;
        }

        $channel_url = parse_url($this->link);
        if (!isset($channel_url["scheme"], $channel_url["host"])) {
            return $link;
        }

        $origin = $channel_url["scheme"] . "://" . $channel_url["host"];
        if (isset($channel_url["port"])) {
            $origin .= ":" . $channel_url["port"];
        }

        return $origin . "/" . ltrim($link, "/");
    }
}
