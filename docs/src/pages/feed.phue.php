<page-query>
    *(_type=="chapter")
    order(_file.modified desc)
    limit(10)
</page-query>

<?php

use slowfoot\feed;

$site_url = rtrim($props->globals->config->site_url, "/");
$site_prefix = rtrim($props->globals->config->path_prefix, "/");
$feed = new feed("slowfoot documentation", $site_url . $site_prefix . "/");
$feed->items(function ($it) use ($helper, $site_url) {
    return [
        "title" => $it->title,
        "pubDate" => $it->_file->modified,
        "link" => $site_url . "/" . ltrim((string) $helper->path($it), "/")
    ];
});

print $feed->generate($props->page);

// var_dump($props->page);
