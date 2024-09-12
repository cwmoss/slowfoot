<?php

namespace slowfoot_plugin\markdown;

use slowfoot\configuration;
use Mni\FrontYAML\Parser;
use slowfoot\file_meta;

class loader {

    public function __construct(
        public string $file,
        public ?string $remove_prefix = null
    ) {
    }

    public function __invoke(configuration $config) {
        // $me = $config->get_plugin(self::class);
        $me = $this;
        $front = new Parser;
        $filep = $config->base . '/' . $me->file;
        dbg("++ md glob:", $filep);
        $prefix = $config->base . '/';

        $files = globstar($filep);
        foreach ($files as $f) {
            // dbg("++ md file:", $f);
            $meta = new file_meta($f, $config->base, remove_prefix: $this->remove_prefix);
            $doc = $meta->get_document();

            $document = $front->parse($doc->content, false);
            $data = $document->getYAML() ?? [];
            $md = $document->getContent() ?? '';

            $data["slug"] ??= $doc->_id;
            $data["mdbody"] = $md;
            $doc->update($data);
            unset($doc["content"]);
            yield $doc;
        }
        return;
    }

    public function x__invoke(configuration $config) {
        // $me = $config->get_plugin(self::class);
        $me = $this;
        $front = new Parser;
        $filep = $config->base . '/' . $me->file;
        dbg("++ md glob:", $filep);
        $prefix = $config->base . '/';

        $files = globstar($filep);
        foreach ($files as $f) {
            // dbg("++ md file:", $f);

            $fullname = str_replace($prefix, '', $f);
            $fname = $fullname;
            if ($me->remove_prefix) {
                $fname = preg_replace("~^{$me->remove_prefix}~", "", $fname);
            }
            $path_parts = pathinfo($fname);

            $document = $front->parse(file_get_contents($f), false);
            $data = $document->getYAML() ?? [];
            $md = $document->getContent() ?? '';
            #$id = $data['_id']??($data['id']??$fname);
            $id = ltrim($path_parts['dirname'] . '/' . $path_parts['filename'], "/.");
            $data["slug"] ??= $id;
            // dbg("+mdloader ID", $id, $path_parts);
            // TODO: anything goes
            // $id = str_replace('/', '-', $id);
            $row = array_merge($data, [
                'mdbody' => $md,
                '_id' => $id,
                // '_rfile' => $f,
                // '_info' => $path_parts,
                '_file' => [
                    'path' => $fname,
                    'full' => $fullname,
                    'dir' => ltrim($path_parts['dirname'], "./"),
                    'name' => $path_parts['filename'],
                    'ext' => $path_parts['extension']
                ]
            ]);
            yield $row;
        }
        return;
    }
}
