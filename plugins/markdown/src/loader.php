<?php

namespace slowfoot_plugin\markdown;

use DateTime;
use slowfoot\configuration;
use Mni\FrontYAML\Parser;
use slowfoot\file_meta;

class loader {

    public function __construct(
        public string $file,
        public ?string $remove_prefix = null,
        public ?string $date_input_format = "d.m.Y"
    ) {
    }

    public function __invoke(configuration $config) {
        // $me = $config->get_plugin(self::class);
        $me = $this;
        $front = new Parser;
        $filep = $config->base . '/' . $me->file;
        dbg("++ md glob:", $filep);

        $files = globstar($filep);
        foreach ($files as $f) {
            // dbg("++ md file:", $f);
            $meta = new file_meta($f, $config->base, remove_prefix: (string) $this->remove_prefix);
            $doc = $meta->get_document(type: "");

            $document = $front->parse($doc->content, false);
            $data = $document->getYAML() ?? [];
            $md = $document->getContent() ?? '';

            if ($data["date"] ?? null) {
                $date = DateTime::createFromFormat($this->date_input_format, $data["date"]);
                $data["date"] = $date->format("Y-m-d H:i:s");
            }
            $data["slug"] ??= $doc->_id;
            $data["mdbody"] = $md;
            $doc->update($data);
            unset($doc["content"]);
            yield $doc;
        }
        return;
    }
}
