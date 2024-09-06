<?php

namespace slowfoot\functions;

/*
    must be php 7.4 compatible (for now)
*/

class runner {

    public string $base;

    public function __construct(string $base) {
        $this->base = $base;
    }

    public function run($url) {
        $req = new request($url);
        $resp = new response();
        $cname = $req->method . "_" . $req->name;
        $file = $this->base . "/$cname" . ".php";
        if (file_exists($file)) {
            include($file);
            $handler = new $cname;
            $resp = $handler($req, $resp);
        } else {
            $resp->not_found();
        }
        $resp->emit();
    }
}
