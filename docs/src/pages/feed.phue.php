<page-query>
    *(_type=="chapter")
    order(_file.modified desc)
    limit(10)
</page-query>

<?php

use slowfoot\feed;
use slowfoot\feed_item;

$site_url = rtrim($props->globals->config->site_url, "/");
$site_prefix = rtrim($props->globals->config->path_prefix, "/");
$feed = new feed("slowfoot documentation", $site_url . $site_prefix . "/");
$feed->items(function ($it) use ($helper) {
    return new feed_item(
        title: $it->title,
        link: $helper->path($it),
        pubDate: $it->_file->modified
    );
});

print $feed->generate($props->page);

// var_dump($props->page);
