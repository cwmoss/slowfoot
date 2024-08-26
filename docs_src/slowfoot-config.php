<?php
require_once("site.php");

use slowfoot\configuration;
use slowfoot\loader\dataset;
use slowfoot\store;
use slowfoot_plugin\markdown;

return new configuration(
    site_name: 'slowfoot Documentation',
    site_description: 'Docs for slowfoot',
    path_prefix: "/slowfoot",
    store: "memory",
    sources: [
        "chapter" => new markdown\loader('content/**/*.md', remove_prefix: "content/"),
        'chapter_index' => site::load_chapter_index(...)
    ],
    templates: [
        'chapter' => '/:_id',
    ],
    plugins: [
        new site(),
        new markdown\markdown_plugin(),
    ],
    build: "../docs"
);
