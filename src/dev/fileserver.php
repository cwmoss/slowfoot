<?php

namespace slowfoot\dev;

use FrameworkX\Io\HtmlHandler;
use FrameworkX\Io\RedirectHandler;
use FrameworkX\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class fileserver {

    /**
     * Mapping between file extension and MIME type to send in `Content-Type` response header
     *
     * @var array<string,string>
     */
    private $mimetypes = array(
        'atom' => 'application/atom+xml',
        'bz2' => 'application/x-bzip2',
        'css' => 'text/css',
        'gif' => 'image/gif',
        'gz' => 'application/gzip',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ico' => 'image/x-icon',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'rss' => 'application/rss+xml',
        'svg' => 'image/svg+xml',
        'tar' => 'application/x-tar',
        'xml' => 'application/xml',
        'zip' => 'application/zip',
    );

    /**
     * Assign default MIME type to send in `Content-Type` response header (same as nginx/Apache)
     *
     * @var string
     * @see self::$mimetypes
     */
    private $defaultMimetype = 'text/plain';

    /** @var ErrorHandler */
    private $errorHandler;

    /** @var HtmlHandler */
    private $html;

    public function __construct(private string $root, public array $rewrite = []) {
        $this->errorHandler = new ErrorHandler();
        $this->html = new HtmlHandler();
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface {
        $local = $request->getAttribute('path', '');
        // $dsname = $request->getAttribute('dsname', '');
        $dsname = "";
        dbg("+++ SPA path", $local, $dsname, $this->root);

        if ($dsname) $local = $dsname . "/frontend/" . $local;
        $count = 0;
        foreach ($this->rewrite as $rule) {
            $local = preg_replace($rule[0], $rule[1], $local, 1, $count);
            if ($count) break;
        }
        assert(\is_string($local));
        $path = \rtrim($this->root . '/' . $local, '/');

        // local path should not contain "./", "../", "//" or null bytes or start with slash
        $valid = !\preg_match('#(?:^|/)\.\.?(?:$|/)|^/|//|\x00#', $local);
        if (!$valid) return $this->errorHandler->requestNotFound();



        \clearstatcache();
        if (\is_dir($path) || !\is_file($path)) {
            $path = $this->root . '/index.html';
        }

        #if ($local !== '' && \substr($local, -1) === '/') {
        #    return (new RedirectHandler('../' . \basename($path)))();
        #}

        // Assign MIME type based on file extension (same as nginx/Apache) or fall back to given default otherwise.
        // Browsers are pretty good at figuring out the correct type if no charset attribute is given.
        $ext = \strtolower(\substr($path, \strrpos($path, '.') + 1));
        $headers = [
            'Content-Type' => $this->mimetypes[$ext] ?? $this->defaultMimetype
        ];

        $stat = @\stat($path);
        if ($stat !== false) {
            $headers['Last-Modified'] = \gmdate('D, d M Y H:i:s', $stat['mtime']) . ' GMT';

            if ($request->getHeaderLine('If-Modified-Since') === $headers['Last-Modified']) {
                return new Response(Response::STATUS_NOT_MODIFIED);
            }
        }

        return new Response(
            Response::STATUS_OK,
            $headers,
            \file_get_contents($path) // @phpstan-ignore-line TODO handle error if file can not be accessed
        );
    }
}
