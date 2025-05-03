<?php
require_once("site.php");

use slowfoot\configuration;
use slowfoot\loader\dataset;
use slowfoot\store;
use slowfoot_plugin\markdown;
use slowfoot_plugin\phuety\phuety_adapter;

return new configuration(
    site_name: 'slowfoot Documentation',
    site_description: 'Docs for slowfoot',
    path_prefix: "/slowfoot",
    // store: "memory",
    sources: [
        "chapter" => new markdown\loader('content/**/*.md', remove_prefix: "content/"),
        'chapter_index' => site::load_chapter_index(...)
    ],
    templates: [
        'chapter' => '/:slug',
    ],
    plugins: [
        new site(),
        new markdown\markdown_plugin(),
    ],
    template_engine: phuety_adapter::class,
    // build: "../docs"
    hooks: [
        /*    "after_build" => function () {
            $dist = __DIR__ . "/dist/";
            $docs = __DIR__ . "/../docs/";
            `rsync -avz --delete $dist $docs`;
        }
            */]
);
