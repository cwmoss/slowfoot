<?php

namespace slowfoot\loader;

use Exception;
use slowfoot\configuration;
use OviDigital\JsObjectToJson\JsConverter;
use RuntimeException;

class csv {

    public function __construct(
        public string $file,
        public string $separator = ',',
        public string $enclosure = '"',
        public bool $json = false,
        public bool $jsol = false
    ) {
        if ($jsol && !class_exists(JsConverter::class)) {
            throw new Exception("JsConverter class is missing. please install with: composer require ovidigital/js-object-to-json");
        }
    }
    public function __invoke(configuration $config) {
        $file = $config->base . '/' . $this->file;
        $handle = fopen($file, "r");
        if ($handle === false) throw new RuntimeException("could not open csv file $file");

        $header = fgetcsv($handle, null, $this->separator, $this->enclosure, "");
        $header[0] = file::remove_bom($header[0]);

        while (($data = fgetcsv($handle, null, $this->separator, $this->enclosure, "")) !== false) {

            if ($this->json) {
                $data = array_map(fn($val) => json_decode($val, true), $data);
            }
            if ($this->jsol) {
                $data = array_map(function ($val) {
                    if ($val[0] == '[' || $val[0] == '{') {
                        return json_decode(JsConverter::convertToJson($val), true);
                    } else {
                        return $val;
                    }
                }, $data);
            }
            //print_r($data);
            //return [];
            yield array_combine($header, $data);
        }
        fclose($handle);
    }
}
