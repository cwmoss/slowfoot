<?php

use slowfoot\configuration;
use slowfoot\loader\dataset;
use slowfoot\store;
use slowfoot\plugins;
use slowfoot\plugins\markdown;

function load_chapter_index(configuration $config, store $db) {
    $chapters = $db->query('chapter() order(_file.path)');
    //$current_section = $current['dir']?basename($current['dir']):basename($chapters[0]['_file']['dir']);
    $current_section = "";
    $chapters = array_reduce($chapters, function ($res, $chapter) use ($current_section) {
        $sid = basename($chapter['_file']['dir']);
        if (!isset($res[$sid])) {
            $res[$sid] = [
                'sid' => $sid,
                'title' => $chapter['chapter_title'] ?? $sid,
                'active' => $sid == $current_section,
                'c' => [$chapter]
            ];
        } else {
            $res[$sid]['c'][] = $chapter;
        }
        return $res;
    }, []);
    yield ['_id' => 'chapter_index', 'index' => $chapters];
    return;
}

return new configuration(
    site_name: 'slowfoot Documentation',
    site_description: 'Docs for slowfoot',
    store: "memory",
    sources: [
        "chapter" => markdown::data_loader(...),
        // 'markdown' => [
        //     'file' => 'content/**/*.md',
        //     'type' => 'chapter'
        // ],
        'chapter_index' => load_chapter_index(...)
    ],
    templates: [
        'chapter' => '/:_file.name',
    ],
    plugins: [
        new markdown('content/**/*.md')
    ],
    build: "../docs"
);


return [
    'site_name' => 'slowfoot Documentation',
    'site_description' => 'Docs for slowfoot',
    'site_url' => '',
    'path_prefix' => '/slowfoot-lib',
    'title_template' => '',
    //'store' => 'sqlite',
    'sources' => [

        'markdown' => [
            'file' => 'content/**/*.md',
            'type' => 'chapter'
        ],
        'chapter_index' => null
    ],
    'templates' => [
        'chapter' => '/:_file.name',
    ],
    'plugins' => [
        'markdown'
    ],
    'build' => '../docs'
];
