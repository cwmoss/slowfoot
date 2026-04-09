<?php

namespace slowfoot\dev;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class timer {
    public function __invoke(ServerRequestInterface $request, callable $next) {
        // optionally return response without passing to next handler
        // return React\Http\Message\Response::plaintext("Done.\n");

        // optionally modify request before passing to next handler
        // $request = $request->withAttribute('admin', false);

        // call next handler in chain
        $start =  microtime(true);
        $response = $next($request);
        $elapsed = $this->elapsed($start);
        // assert($response instanceof ResponseInterface);

        // optionally modify response before returning to previous handler
        // $response = $response->withHeader('Content-Type', 'text/plain');

        return $response->withAddedHeader("x-time-elapsed", $elapsed["time_print"]);
    }

    public function elapsed(float $start): array {
        $elapsed = microtime(true) - $start;
        $data = [
            "time" => $elapsed,
            "time_ms" => (int)($elapsed * 1000),
            "time_microsec" => (int)($elapsed * 1000 * 1000),
        ];
        $data['time_print'] = $data['time_ms'] ?
            $data['time_ms'] . ' ms' : $data['time_microsec'] . ' us';

        return $data;
    }
}
