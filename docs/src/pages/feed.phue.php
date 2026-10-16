<page-query>
    *(_type=="chapter")
    order(_file.modified desc)
    limit(10)
</page-query>

<?php

use slowfoot\feed;

$feed = new feed("slowfoot documentation", "https://cwmoss.github.io/");
$feed->items(function ($it) use ($helper) {
    return [
        "title" => $it->title,
        "pubDate" => $it->_file->modified,
        "link" => $helper->path($it)
    ];
});

print $feed->generate($props->page);

// var_dump($props->page);
