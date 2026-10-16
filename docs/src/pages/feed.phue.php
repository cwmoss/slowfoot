<page-query>
    *(_type=="chapter")
    order(_file.modified desc)
    limit(10)
</page-query>

<?php

use slowfoot\feed;

$feed = new feed("slowfoot documentation");
$feed->items(function ($it) {
    return [
        "title" => $it->title,
    ];
});

print $feed->generate($props->page);

// print_r($props->page);
