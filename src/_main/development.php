<?php
define('SLOWFOOT_START', microtime(true));
error_reporting(E_ALL ^ E_DEPRECATED);
# error_reporting(E_ALL);
ini_set("display_errors", 0);
/*
TODO: vendor-dir vs project-dir
*/
$project_dir = $_SERVER['DOCUMENT_ROOT'] . '/../';
define('SLOWFOOT_BASE', $project_dir);
$PDIR = $project_dir;

//require $project_dir . '/vendor/autoload.php';
use Bramus\Router\Router;
use slowfoot\context;
use slowfoot\pagebuilder;
use slowfoot\configuration;
use slowfoot\store;
use slowfoot\util\server;
use wrun\runner;

$IS_PROD = false;

/** @var project $project */
$project = require __DIR__ . '/../_boot.php';

ini_set("precision", 16);
define('START_TIME', microtime(true));

error_reporting(E_ALL & ~E_NOTICE);

if (PHP_SAPI == 'cli-server') {
    if (strpos($_SERVER['REQUEST_URI'], '.') !== false) {
        #dbg('+++ env hack!');
        $_SERVER['SCRIPT_NAME'] = '/' . basename($_SERVER['SCRIPT_FILENAME']);
    }
}

$router = new Router();
$router->setBasePath("");
$hr = false;
$debug = true;

$router->mount('/__api', function () use ($router, $project) {
    #dbg('server', $_SERVER);
    server::send_cors();

    $router->post("/fetch", function () use ($project) {
        $project->config->fresh_store();
        $project->load();
        server::resp(["ok" => true]);
    });

    $router->get('/index', function () use ($project) {

        #print "hallo";

        //$rows = $db->run('SELECT * FROM docs LIMIT 20');
        // $rows = $db->run('SELECT _type, count(*) AS total FROM docs GROUP BY _type');

        server::resp($project->ds->info());
    });

    $router->get('/type/([-\w.]+)(/\d+)?', function ($type, $page = 1) use ($project) {
        dbg("[api] type", $type);
        #print "hallo";
        if ($type == '__paths') {
            if ($project->ds->db->db)
                $rows = $project->ds->db->db->safeQuery('SELECT * FROM paths LIMIT ? OFFSET ?', [20, 0]);
            else $rows = array_values(array_map(fn($p) => [
                "id" => $p["_"],
                "path" => $p["_"],
                "name" => "_"
            ], $project->ds->db->paths));
        } else {
            $rows = $project->ds->query_type($type);
        }
        //$rows = $db->run('SELECT * FROM docs LIMIT 20');
        //$rows = $db->q('SELECT _id, body FROM docs WHERE _type = ? LIMIT 20', $type);

        server::resp(['rows' => $rows]);
    });

    $router->get('/id', function () use ($project) {
        $id = $_GET['id'];
        //$row = $db->row('SELECT _id, _type, body FROM docs WHERE _id = ? ', $id);
        $row = $project->ds->get($id);
        server::resp($row);
    });

    $router->get('/lolql', function () use ($project) {
        $query = trim($_GET['query'] ?? "");
        //$row = $db->row('SELECT _id, _type, body FROM docs WHERE _id = ? ', $id);
        $rows = $project->ds->query($query);
        server::resp($rows);
    });

    $router->get('/fts', function () use ($project) {
        $q = $_GET['q'];
        $rows = $project->ds->q("SELECT _id, snippet(docs_fts,1, '<b>', '</b>', '[...]', 30) body FROM docs_fts WHERE docs_fts = ? ", $q);
        server::resp($rows);
    });


    $router->get('/preview/(.*)', function ($id_type) use ($project) {
        list($id, $type) = explode('/', $id_type);
        dbg("[api/preview]", $id_type);

        $preview_obj =  []; // load_preview_object($id, $type, $config);

        #$template = $templates[$obj['_type']]['_']['template'];
        #$template = template_name($config['templates'], $obj['_type'], $name);
        #dbg('[api/preview] template', $preview_obj);
        $context = [
            'mode' => 'dev',
            'src' => $project->src,
            'path' => $id_type,
            'site_name' => $project->config['site_name'] ?? '',
            'site_description' => $project->config['site_description'] ?? '',
            'site_url' => $project->config['site_url'] ?? '',

        ];
        // TODO: migrate
        // $content = template($preview_obj['template'], ['page' => $preview_obj['data']], $template_helper, template_context('template', $context, $preview_obj, $ds, $config));
        $content = "";
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: text/html');

        print $content;
    });
});

$router->mount('/__ui', function () use ($router) {
    $router->get('', function () {
        $uibase = __DIR__ . '/../../ui';
        dbg("+++ ui index ++++", $uibase);
        server::send_file($uibase, 'index.html');
        exit;
    });

    $router->get('(.*)?', function ($file) {
        $uibase = __DIR__ . '/../../ui';
        $uifile = $uibase . '/' . $file;
        dbg("__ui file00", $file, $uifile);

        if (file_exists($uifile)) {
            server::send_file($uibase, $file);
            exit;
        } else {
            server::send_file($uibase, 'index.html');
            exit;
        }
        dbg("__ui file", $file, $uifile);
        server::resp(['ok' => $file]);
    });
});

$router->get('/__sf/(.*)', function ($requestpath) {
    $docbase = __DIR__ . '/../../resources';
    server::send_file($docbase, $requestpath);
    exit;
});

$router->post('/__fun/(.*)', function ($requestpath) {
    $docbase = $_SERVER['DOCUMENT_ROOT'] . '/../endpoints';
    include($docbase . "/" . $requestpath);
    exit;
});

$router->all('/__run/(.*)', function ($requestpath) {
    $funbase = $_SERVER['DOCUMENT_ROOT'] . '/../server-functions';
    $runner = new runner($funbase);
    $runner->run($requestpath);
});

#dbg("++ image path", $config['assets']['path']);

$router->get($project->config->assets->path . '/' . '(.*\.\w{1,5})', function ($requestpath) {
    dbg('[dev] asssets', $requestpath);
    $docbase = $_SERVER['DOCUMENT_ROOT'] . '/../var/rendered-images';
    #dbg("++ image path base", $docbase, $requestpath);
    server::send_file($docbase, $requestpath);
    exit;
});

$router->get('(.*\.\w{1,5})', function ($requestpath) {
    $docbase = $_SERVER['DOCUMENT_ROOT'];
    dbg('[dev] some.doc', $requestpath);
    server::send_file($docbase, $requestpath);
    exit;
});

$router->get('(.*)?', function ($requestpath) use ($project) {
    dbg('[dev] page/template', $requestpath);
    server::send_nocache();
    $requestpath = '/' . $requestpath;
    // startseite?
    if ($requestpath == '/' || $requestpath == '') {
        $requestpath = '/index';
    }
    $builder = new pagebuilder($project->config, $project->ds, $project->template_helper);
    #dbg("dev: req", $requestpath);
    [$obj_id, $name] = $project->ds->get_by_path($requestpath);
    dbg("dev ID - name", $obj_id, $name, $requestpath);
    $context = new context(
        mode: 'dev',
        src: $project->src,
        path: $requestpath,
        config: $project->config
    );

    if ($obj_id) {
        $content = $builder->make_template($name, $context, $obj_id);
    } else {
        list($dummy, $pagename, $pagenr) = explode('/', $requestpath) + [2 => 0];
        $pagename = '/' . $pagename;
        if ($pagename == '/') {
            //    $pagename='/index';
            // $pagename = "/index";
        }

        dbg('page...', $pagename, $pagenr, $requestpath, $project->pages);
        $obj_id = array_search($pagename, $project->pages);
        $content = $builder->make_page($pagename, $pagenr, $requestpath, $context);
    }
    $debug = true;
    if ($debug) {
        $inspector = include_to_buffer(__DIR__ . '/../../resources/debug.php');
        $inspector_head = '<script defer src="/__sf/json-viewer.bundle.js"></script>';
        // $inspector_css = '<link rel="stylesheet" href="/__sf/inspector-json.css">';
        //$content = str_replace('</head>', $inspector_head . '</head>', $content);
        $content = str_replace('</body>', $inspector . '</body>', $content);
    }
    print $content;
    exit;
});

$router->set404(function () {
    // dbg('-- 404');
    server::e404();
});

$router->run();
exit;


if ($hr) {
    require_once __DIR__ . '/hot-reload/HotReloader.php';
    $htrldr = new HotReloader\HotReloader('//localhost:1199/phrwatcher.php');
    $js = $htrldr->init();
    $content = str_replace('</html>', $js . '</html>', $content);
}
