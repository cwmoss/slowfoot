<?php

namespace slowfoot\functions;

class request {
    public $name;
    public $method;
    public $get;
    public $post;
    public array $body = [];

    public function __construct($url) {
        $this->name = $url;
        $this->method = strtolower($_SERVER["REQUEST_METHOD"]);
        $this->get = $_GET;
        $this->post = $_POST;
        $body = file_get_contents('php://input');
        if ($body) $this->body = json_decode($body, true);
    }
}
