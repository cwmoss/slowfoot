<?php

namespace slowfoot\util;

use OutOfBoundsException;

class server {

    /*
    files related
*/

    /*
    creates a auto-delete tempfile
    returns the name
    substitude for tempnam($dir, $prefix)
*/
    static public function tempfilename($dir = "", $prefix = "") {
        # return stream_get_meta_data(tmpfile())['uri'];
        $file = tempnam($dir, $prefix);
        //register_shutdown_function(fn () => @unlink($file));
        register_shutdown_function(function () use ($file) {
            @unlink($file);
        });
        return $file;
    }

    static public  function normalize_files_array($files = []) {
        $normalized_array = [];

        foreach ($files as $index => $file) {
            if (!is_array($file['name'])) {
                $normalized_array[$index][] = $file;
                continue;
            }

            foreach ($file['name'] as $idx => $name) {
                $normalized_array[$index][$idx] = [
                    'name' => $name,
                    'type' => $file['type'][$idx],
                    'tmp_name' => $file['tmp_name'][$idx],
                    'error' => $file['error'][$idx],
                    'size' => $file['size'][$idx]
                ];
            }
        }

        return $normalized_array;
    }

    static public function text_for($muster, $vars = array()) {
        $repl = array();
        foreach ($vars as $k => $v) {
            $repl['{' . strtolower($k) . '}'] = $v;
        }
        $txt = $muster;
        $txt = str_replace(array_keys($repl), $repl, $txt);
        return $txt;
    }



    static public function shell_command($cmd, $parms, $opts = []) {
        $parms = array_map('escapeshellarg', $parms);
        $cmd = text_for($cmd, $parms);
        # $ok = shell_exec($cmd);

        exec($cmd, $output, $ok);
        if ($ok !== 0) {
            #e500("shell command failed: $cmd");
        }
        return [$output, $ok];
    }


    /**
    http related
    TODO: response object, meta/timer object
     */
    static public function resp($data) {
        $elapsed = microtime(true) - SLOWFOOT_START;
        $data = (array) $data;
        if (!isset($data['res'])) {
            $data = ['res' => $data];
        }
        if (isset($data['__meta'])) {
            $data['__meta']['time'] = $elapsed;
        } else {
            $data['__meta'] = ['time' => $elapsed];
        }
        $data['__meta']['time_ms'] = (int)($elapsed * 1000);
        $data['__meta']['time_microsec'] = (int)($elapsed * 1000 * 1000);
        $data['__meta']['time_print'] = $data['__meta']['time_ms'] ?
            $data['__meta']['time_ms'] . ' ms' : $data['__meta']['time_microsec'] . ' μs';
        header('Content-Type: application/json'); //; charset=utf-8
        print json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        dbg('+++ finished');
    }

    static public function e404($msg = 'not found') {
        header('HTTP/1.1 404 Not Found');
        self::resp(['fail' => $msg]);
    }

    static public function e401($msg = 'unauthorized api request') {
        dbg('+++ 401 +++ ');
        header('HTTP/1.1 401 Unauthorized');
        self::resp(['fail' => $msg]);
        exit;
    }

    static public function e500($msg = 'fatal error') {
        dbg('+++ 500 +++ ');
        header('HTTP/1.1 500 Bad Request');
        self::resp(['fail' => $msg]);
        exit;
    }

    static public function get_json_and_raw_req() {
        $raw = self::get_raw_req();
        $post = json_decode($raw, true);
        return [$post, $raw];
    }

    static public function get_json_req() {
        return json_decode(self::get_raw_req(), true);
    }

    static public function get_raw_req() {
        dbg('++++ raw input read ++++');
        return file_get_contents('php://input');
    }

    static public function get_req_headers($router) {
        dbg('+++ router get headers');
        return array_change_key_case($router->getRequestHeaders());
    }

    static public function url_to_pdo_dsn($url) {
        $parts = parse_url($url);

        return [
            $parts['scheme'] . ':host=' . $parts['host'] . ';dbname=' . trim($parts['path'], '/'),
            $parts['user'],
            $parts['pass']
        ];
    }


    static public function send_cors() {
        $orig = @$_SERVER['HTTP_ORIGIN'];
        header('Access-Control-Allow-Origin: ' . $orig);
        header('Access-Control-Allow-Methods: POST, GET, HEAD, PATCH, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        if (array_key_exists('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', $_SERVER)) {
            header(
                'Access-Control-Allow-Headers: '
                    . 'Authorization, Origin, X-Requested-With, X-Request-ID, X-HTTP-Method-Override, Content-Type, Upload-Length, Upload-Offset, Tus-Resumable, Upload-Metadata'
                //   . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']
            );
        } else {
            //   header('Access-Control-Allow-Headers: *');
        }

        header('Access-Control-Allow-Credentials: true');
        //  header('Access-Control-Allow-Headers: Authorization');
        header('Access-Control-Expose-Headers: Upload-Key, Upload-Checksum, Upload-Length, Upload-Offset, Upload-Metadata, Tus-Version, Tus-Resumable, Tus-Extension, Location');
    }

    static public function send_nocache() {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: text/html');
    }

    static public function find_free_port(int $start = 10101, string $host = 'localhost'): int {
        // max trials
        $end = $start + 20;
        for ($port = $start; $port < $end; $port++) {
            $connection = @fsockopen($host, $port);
            if (is_resource($connection)) {
                fclose($connection);
                return $port;
            }
        }
        throw new OutOfBoundsException("could not find a free port");
    }

    static public function send_file($base, $file) {
        $name = basename($file);
        $full = $base . '/' . $file;

        if (!file_exists($full)) {
            header('HTTP/1.1 404 Not Found');
            return;
        }

        $ext = pathinfo($name, PATHINFO_EXTENSION);

        $types = [
            'css' => 'text/css',
            'js'   => 'text/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'html' => 'text/html',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf'
        ];

        $type = $types[$ext];

        if ($ext == 'css') {
            $scss = $full . '.scss';
            if (file_exists($scss)) {
                // die(" sassc $scss $full");
                //print "sassc $scss $full";
                $resp = self::shell_command('sassc {in} {out} 2>&1', ['in' => $scss, 'out' => $full]);
                // $ok = `sassc $scss $full`;
                if ($resp[1] !== 0) {
                    dbg("[sassc] error", $resp);
                }
                //var_dump($ok);
            }
        }
        header('Content-Type: ' . $type);

        if ($ext == 'html') {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Content-Type: text/html');
        }

        readfile($full);
    }

    static public function send_asset_file($base, $file, $orig, $cache) {
        $full = $base . '/' . $file;
        $full = str_replace($orig, $cache, $full);
        dbg('+++ asset route', $full);
        //print "$full";
        //exit;
        header('Content-Type: image/jpg');
        if (file_exists($full)) {
            readfile($full);
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }
}
