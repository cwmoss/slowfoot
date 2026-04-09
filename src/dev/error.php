<?php

namespace slowfoot\dev;

use React\Http\Message\Response as P;
use Psr\Http\Message\ServerRequestInterface;
use slowfoot\error_handler;
use slowfoot\util\html;
use Throwable;

class error {

    public function __invoke(ServerRequestInterface $request, callable $next): P {
        try {
            return $next($request);
        } catch (Throwable $e) {
            $page = error_handler::dev_exeption_page($e);
            // dbg("+++ page result", $page);
            return new P(
                400,
                [
                    'Content-Type' => 'text/html; charset=utf-8'
                ],
                $page[0]
            );
        }
    }
}
