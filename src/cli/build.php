<?php

use slowfoot\util\console;
use slowfoot\context;
use slowfoot\pagebuilder;

/** @var project $project */

$console = console::console();

print memory_get_usage() . " loaded ok\n";

if (!$project->dist()) {
    die('NO DIST-PATH FOUND');
}

$dist = $project->dist();
print "DIST: $dist";

print console::console_table(['_type' => 'type', 'total' => 'total'], $project->info_types());

shell_info("removing old dist/ folder");
`rm -rf $dist`;
shell_info();

$context = new context(
    mode: 'build',
    src: $project->src,
    path: "",
    config: $project->config
);

shell_info("writing templates", true);

dbg("+++ PREFIX", PATH_PREFIX);
// print_r($project);

$builder = $project->builder();
$dist = $project->dist();
$ds = $project->ds;

foreach ($project->templates() as $type => $conf) {
    //$count = query('');
    //if($type=='article') continue;
    $bs = 100;
    $start = 0;

    shell_info("  => $type");

    // TODO
    foreach (query_type($project->ds, $type) as $row) {
        foreach ($conf as $templateconf) {
            //	process_template_data($row, path($row['_id']));
            $path = $ds->get_fpath($row->_id, $templateconf['name']);
            if ($path == '/index') {
                $path = '/';
            }
            if ($path == "/") {
                #var_dump($row);
                #exit;
            }
            $context->path = $path;
            $content = $builder->make_template(
                $templateconf['template'],
                $context,
                data: $row,
                template_conf: $templateconf
            );
            write($content, $path, null, $dist);
        }
    }
    shell_info();
}

shell_info("writing pages", true);

foreach ($project->pages as $pagename) {
    shell_info("  => $pagename");
    $pagepath = $pagename;
    if ($pagepath == '/index') {
        $pagepath = '/';
    }
    $generator = $builder->make_page_bulk($pagename, $context);
    foreach ($generator as $result) {
        write($result["content"], $pagepath, $result["pagenr"], $dist);
    }
    shell_info();
}

shell_info("copy assets");

`cp -R {$project->src}/assets {$project->dist()}/`;
`cp -a {$project->config->var}/rendered-images/. {$project->dist()}/images`;

shell_info();

if (isset($project->config->hooks['after_build'])) {
    shell_info("after build hook");
    $project->config->hooks['after_build']($project->config);
    shell_info();
} else {
    shell_info("no after build hook configured", true);
}

// print getenv("SLFT_WRITE_PATH");
shell_info("⚡️ done", true);
