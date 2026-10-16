<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use slowfoot\feed;
use slowfoot\feed_item;

final class FeedTest extends TestCase {

    public function testTypedItemsAreSerialized(): void {
        $feed = new feed("Docs");
        $feed->items(fn($item) => new feed_item(
            title: "A & B",
            link: "https://example.test/article",
            pubDate: 1746455011
        ));

        $xml = simplexml_load_string($feed->generate([[]]));

        $this->assertNotFalse($xml);
        $this->assertSame("A & B", (string) $xml->channel->item->title);
        $this->assertSame("https://example.test/article", (string) $xml->channel->item->link);
        $this->assertSame("Mon, 05 May 2025 14:23:31 +0000", (string) $xml->channel->item->pubDate);
    }

    public function testArrayItemsRemainSupported(): void {
        $feed = new feed("Docs");
        $feed->items(fn($item) => ["title" => "Legacy item"]);

        $xml = simplexml_load_string($feed->generate([[]]));

        $this->assertNotFalse($xml);
        $this->assertSame("Legacy item", (string) $xml->channel->item->title);
    }

    public function testRelativeItemLinksUseTheChannelOrigin(): void {
        $feed = new feed("Docs", "https://example.test/slowfoot/");
        $feed->items(fn($item) => new feed_item(
            title: "Article",
            link: "/slowfoot/article"
        ));

        $xml = simplexml_load_string($feed->generate([[]]));

        $this->assertNotFalse($xml);
        $this->assertSame("https://example.test/slowfoot/article", (string) $xml->channel->item->link);
    }
}
