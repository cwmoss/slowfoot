<?php

namespace slowfoot\loader;

class file {

    public function __construct(
        public ?string $file = null,

    ) {
    }


    static public function file_to_document() {
    }

    static function remove_bom(string $str) {
        $bom = "\xef\xbb\xbf";
        if (str_starts_with($str, $bom)) return substr($str, 3);
        return $str;
    }
}
