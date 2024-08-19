<?php

namespace slowfoot\image;
/*
          '_type' => 'slft.asset',
            '_id' => $path,
            '_src' => $this->conf->src,
            'url' => $fname,
            'path' => $fname,
            'w' => $info[0],
            'h' => $info[1],
            'mime' => $info['mime']
            */

class asset {
    public string $_type = "slft.asset";
    public ?bool $remote_src = false;
    public ?string $download_file = null;
    public ?string $resize_url = null;
    public ?array $resize = null;

    public function __construct(
        public string $_id,
        public string $_src,
        public string $url,
        public string $path,
        public string $w,
        public string $h,
        public string $mime
    ) {
    }
}
