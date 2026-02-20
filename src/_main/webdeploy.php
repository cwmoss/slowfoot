<?php
/*

evtl alternativ via event source
https://developer.mozilla.org/en-US/docs/Web/API/EventSource/EventSource

https://www.py4u.net/discuss/212391
https://stackoverflow.com/questions/56415703/live-execute-git-command-on-php

test via browser console:
    fetch("/", {method:"POST", headers:{"x-slft-deploy":"1234"}})

curl -vv https://yourdomain.com/webdeploy/whatever.php -H "x-slft-deploy: 1234"
*/

#require_once __DIR__.'/../vendor/autoload.php';
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

define('SLOWFOOT_WEBDEPLOY', true);

$deployer = new deployer(
    $_SERVER,
    getenv("SLFT_BUILD_KEY"),
    SLOWFOOT_BASE,
    getenv("SLFT_WRITE_PATH"),
    getenv("SLFT_PATH_PREFIX"),
    getenv("SLFT_PHP_BIN")
);

$deployer->send_cors();
$deployer->check_referer();
$deployer->check_token();
$res = $deployer->build();
$deployer->print_result($res);

/*
    some provider might not have a php cli
    with the right version but a http php SAPI
    so web-deploy/http.php could be used
*/
/*
if ($NOCLI) {
    $FETCH = true;
    
    require __DIR__ . '/boot.php';
    include 'build.php';
    exit;
}
*/
#header('X-Accel-Buffering: no');
#header("Content-Type: text/plain; charset=utf-8");
//header("Content-Type: application/json");
// print $cmd;


class deployer {

    public string $origin;

    public function __construct(
        private array $server,
        private string $token,
        private string $base,
        private string $write_path,
        private string $siteprefix = "",
        private string $php_bin = ""
    ) {
        $this->origin = $server['HTTP_ORIGIN'] ?? $server["HTTP_REFERER"] ?? "";
        if ($php_bin) $this->php_bin .= " ";
    }

    public function build() {
        $cmd = sprintf(
            "%s%s%s/vendor/bin/slowfoot build --colors on -f",
            ($this->write_path ? "SLFT_WRITE_PATH={$this->write_path} " : ""),
            $this->php_bin,
            $this->base
        );
        $converter = new AnsiToHtmlConverter();
        // print $converter->convert("hier \033[1mfett\033[0m text\n");
        // $converter = null;
        return $this->live_execute_command($cmd, true, $converter);
    }

    public function print_result(array $result) {
        if ($result['exit_status'] === 0) {
            // do something if command execution succeeds
            print "ok\n\n";
            #`cd $dir; rsync -avz dist/ ../htdocs/`;
        } else {
            // do something on failure
            print "failed\n\n";
        }

        printf(
            '<a href="%s" target="_slft_preview">Look here</a>',
            '//' . $this->server['HTTP_HOST'] . '/' . $this->siteprefix
        );
    }

    function check_referer() {
        $headers = $this->server;

        // local (dev) installation?
        if ($headers['HTTP_HOST'] == 'localhost') {
            return true;
        }

        if (!isset($headers['SLFT_WEBDEPLOY_ALLOWED_HOSTS'])) {
            return true;
        }

        // $allowed = ['localhost', 'sf-photog.sanity.studio', 'kurparkverlag-gs-studio.netlify.app', 'kurparkverlag.sanity.studio'];
        $allowed = explode(" ", $headers['SLFT_WEBDEPLOY_ALLOWED_HOSTS']);

        # sometimes referer doesn't include the full url (/dashboard)
        # if(!preg_match("!/dashboard$!", $headers['HTTP_REFERER'])) return false;

        $you = $_SERVER["HTTP_ORIGIN"] ?? null;
        if (!$you) $you = $_SERVER["HTTP_REFERER"] ?? null;
        $remote = parse_url($you, PHP_URL_HOST);

        $ok = in_array($remote, $allowed);
        if (!$ok) throw new Exception("failed");
    }

    function check_token() {
        $hdrs = getallheaders();
        $hdrs = array_change_key_case($hdrs);
        $ok = isset($hdrs['x-slft-deploy']) && $this->token && $hdrs['x-slft-deploy'] && $hdrs['x-slft-deploy'] === $this->token;
        if (!$ok) throw new Exception("auth failed");
    }

    function live_execute_command($cmd, $err = false, $converter = null) {
        $lbr = "\n";
        $lbr = "";
        while (@ob_end_flush()); // end all output buffers if any

        if ($err) {
            $cmd .= " 2>&1";
        }
        // $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');
        $proc = popen("$cmd ; echo Exit status : $?", 'r');
        $live_output     = "";
        $complete_output = "";

        while (!feof($proc)) {
            $live_output     = fread($proc, 4096);
            if ($converter) {
                $live_output = $converter->convert($live_output);
            }
            $complete_output = $complete_output . $live_output;
            echo "$live_output"; //  . $lbr . "<br>";
            // echo($converter->convert($live_output.$lbr)."<br>");
            // echo json_encode(['txt'=>$live_output]);
            @flush();
        }

        pclose($proc);

        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);

        // return exit status and intended output
        return array(
            'exit_status'  => intval($matches[0] ?? 0),
            'output'       => str_replace("Exit status : " . ($matches[0] ?? 0), '', $complete_output)
        );
    }

    public function send_cors() {
        // TODO check list;

        header('Access-Control-Allow-Origin: ' . $this->origin);
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        if (array_key_exists('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', $this->server)) {
            header('Access-Control-Allow-Headers: '
                . $this->server['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
        } else {
            //   header('Access-Control-Allow-Headers: *');
        }

        header('Access-Control-Allow-Credentials: true');
        #  header('Access-Control-Allow-Headers: Authorization');
        header('Access-Control-Expose-Headers: Authorization');

        if ("OPTIONS" == $this->server['REQUEST_METHOD']) {
            exit(0);
        }
    }
}
