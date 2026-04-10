<?php
/*
apache:
SetEnv SLFT_BUILD_KEY ycx-sdfsdf-sdf213213-ewrwe-dfs
Alias /__web-deploy /var/www/vhosts/kurparkverlag/slowfoot/web-deploy/index.php

caddy:
    handle_path /__webdeploy/* {
        root * /app/site/webdeploy/
        php_server
    }

request outside docker:

    curl -vv http://localhost:9901/__webdeploy/ -H 'x-slft-deploy: 1234'

*/
/*

evtl alternativ via event source
https://developer.mozilla.org/en-US/docs/Web/API/EventSource/EventSource

https://www.py4u.net/discuss/212391
https://stackoverflow.com/questions/56415703/live-execute-git-command-on-php

test via browser console:
    fetch("/", {method:"POST", headers:{"x-slft-deploy":"1234"}})

curl -vv https://yourdomain.com/webdeploy/whatever.php -H "x-slft-deploy: 1234"
*/

namespace webdeployer;

#require_once __DIR__.'/../vendor/autoload.php';
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Exception;

if (!function_exists('getallheaders')) {

    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * returns The HTTP header key/value pairs.
     */
    function getallheaders(): array {
        $headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }
}


// define('SLOWFOOT_WEBDEPLOY', true);

$deployer = new deployer(
    $_SERVER,
    getenv("SLFT_BUILD_KEY"),
    getenv("SLFT_PROJECT_DIR") ?: dirname(__DIR__),
    getenv("SLFT_WRITE_PATH"),
    getenv("SLFT_PATH_PREFIX"),
    getenv("SLFT_PHP_BIN"),
    getenv("SLFT_DEPLOYER_LBR")
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
    private array $server;
    private string $token;
    private string $base;
    private string $write_path;
    private string $siteprefix = "";
    private string $php_bin = "";
    private string $line_break = "";

    // MUST work with php7
    public function __construct(
        array $server,
        string $token,
        string $base,
        string $write_path,
        string $siteprefix = "",
        string $php_bin = "",
        string $line_break = ""
    ) {
        $this->origin = $server['HTTP_ORIGIN'] ?? $server["HTTP_REFERER"] ?? "";
        $this->server = $server;
        $this->token = $token;
        $this->base = $base;
        $this->write_path = $write_path;
        $this->siteprefix = $siteprefix;
        $this->line_break = $line_break;
        $this->php_bin = $php_bin ?: "slowfoot";
    }

    public function build() {
        $cmd = sprintf(
            "%s%s build -d=%s --colors=on -f",
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
        $lbr = $this->line_break;
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
            echo "$live_output" . $lbr; //  . $lbr . "<br>";
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



/*
 * This file is part of ansi-to-html.
 *
 * (c) 2013 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\AnsiConverter\Theme;

/**
 * Base theme.
 */
class Theme {
    public function asCss($prefix = 'ansi_color') {
        $css = array();
        foreach ($this->asArray() as $name => $color) {
            $css[] = sprintf('.%s_fg_%s { color: %s }', $prefix, $name, $color);
            $css[] = sprintf('.%s_bg_%s { background-color: %s }', $prefix, $name, $color);
        }

        return implode("\n", $css);
    }

    public function asArray() {
        return array(
            'black' => 'black',
            'red' => 'darkred',
            'green' => 'green',
            'yellow' => 'yellow',
            'blue' => 'blue',
            'magenta' => 'darkmagenta',
            'cyan' => 'cyan',
            'white' => 'white',

            'brblack' => 'black',
            'brred' => 'red',
            'brgreen' => 'lightgreen',
            'bryellow' => 'lightyellow',
            'brblue' => 'lightblue',
            'brmagenta' => 'magenta',
            'brcyan' => 'lightcyan',
            'brwhite' => 'white',
        );
    }
}

/*
 * This file is part of ansi-to-html.
 *
 * (c) 2013 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\AnsiConverter;

use SensioLabs\AnsiConverter\Theme\Theme;

/**
 * Converts an ANSI text to HTML5.
 */
class AnsiToHtmlConverter {
    protected $theme;
    protected $charset;
    protected $inlineStyles;
    protected $inlineColors;
    protected $colorNames;

    public function __construct(?Theme $theme = null, $inlineStyles = true, $charset = 'UTF-8') {
        $this->theme = null === $theme ? new Theme() : $theme;
        $this->inlineStyles = $inlineStyles;
        $this->charset = $charset;
        $this->inlineColors = $this->theme->asArray();
        $this->colorNames = array(
            'black',
            'red',
            'green',
            'yellow',
            'blue',
            'magenta',
            'cyan',
            'white',
            '',
            '',
            'brblack',
            'brred',
            'brgreen',
            'bryellow',
            'brblue',
            'brmagenta',
            'brcyan',
            'brwhite',
        );
    }

    public function convert($text) {
        // remove cursor movement sequences
        $text = preg_replace('#\e\[(K|s|u|2J|2K|\d+(A|B|C|D|E|F|G|J|K|S|T)|\d+;\d+(H|f))#', '', $text);
        // remove character set sequences
        $text = preg_replace('#\e(\(|\))(A|B|[0-2])#', '', $text);

        $text = htmlspecialchars($text, PHP_VERSION_ID >= 50400 ? ENT_QUOTES | ENT_SUBSTITUTE : ENT_QUOTES, $this->charset);

        // carriage return
        $text = preg_replace('#^.*\r(?!\n)#m', '', $text);

        $tokens = $this->tokenize($text);

        // a backspace remove the previous character but only from a text token
        foreach ($tokens as $i => $token) {
            if ('backspace' == $token[0]) {
                $j = $i;
                while (--$j >= 0) {
                    if ('text' == $tokens[$j][0] && strlen($tokens[$j][1]) > 0) {
                        $tokens[$j][1] = substr($tokens[$j][1], 0, -1);

                        break;
                    }
                }
            }
        }

        $html = '';
        foreach ($tokens as $token) {
            if ('text' == $token[0]) {
                $html .= $token[1];
            } elseif ('color' == $token[0]) {
                $html .= $this->convertAnsiToColor($token[1]);
            }
        }

        if ($this->inlineStyles) {
            $html = sprintf('<span style="background-color: %s; color: %s">%s</span>', $this->inlineColors['black'], $this->inlineColors['white'], $html);
        } else {
            $html = sprintf('<span class="ansi_color_bg_black ansi_color_fg_white">%s</span>', $html);
        }

        // remove empty span
        $html = preg_replace('#<span[^>]*></span>#', '', $html);

        return $html;
    }

    public function getTheme() {
        return $this->theme;
    }

    protected function convertAnsiToColor($ansi) {
        $bg = 0;
        $fg = 7;
        $as = '';
        if ('0' != $ansi && '' != $ansi) {
            $options = explode(';', $ansi);

            foreach ($options as $option) {
                if ($option >= 30 && $option < 38) {
                    $fg = $option - 30;
                } elseif ($option >= 40 && $option < 48) {
                    $bg = $option - 40;
                } elseif (39 == $option) {
                    $fg = 7;
                } elseif (49 == $option) {
                    $bg = 0;
                }
            }

            // options: bold => 1, underscore => 4, blink => 5, reverse => 7, conceal => 8
            if (in_array(1, $options)) {
                $fg += 10;
                $bg += 10;
            }

            if (in_array(4, $options)) {
                $as = '; text-decoration: underline';
            }

            if (in_array(7, $options)) {
                $tmp = $fg;
                $fg = $bg;
                $bg = $tmp;
            }
        }

        if ($this->inlineStyles) {
            return sprintf('</span><span style="background-color: %s; color: %s%s">', $this->inlineColors[$this->colorNames[$bg]], $this->inlineColors[$this->colorNames[$fg]], $as);
        } else {
            return sprintf('</span><span class="ansi_color_bg_%s ansi_color_fg_%s">', $this->colorNames[$bg], $this->colorNames[$fg]);
        }
    }

    protected function tokenize($text) {
        $tokens = array();
        preg_match_all("/(?:\e\[(.*?)m|(\x08))/", $text, $matches, PREG_OFFSET_CAPTURE);

        $offset = 0;
        foreach ($matches[0] as $i => $match) {
            if ($match[1] - $offset > 0) {
                $tokens[] = array('text', substr($text, $offset, $match[1] - $offset));
            }
            $tokens[] = array("\x08" == $match[0] ? 'backspace' : 'color', $matches[1][$i][0]);
            $offset = $match[1] + strlen($match[0]);
        }
        if ($offset < strlen($text)) {
            $tokens[] = array('text', substr($text, $offset));
        }

        return $tokens;
    }
}
