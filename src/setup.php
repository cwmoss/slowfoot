<?php

namespace slowfoot;

use Exception;

class setup {

    public function __construct(public string $project_dir) {
    }

    public function setup(): bool {
        $writeable = [
            'var',
            'var/download',
            'var/rendered-images',
            'var/template'
        ];

        foreach ($writeable as $dir) {
            $fdir = $this->project_dir . '/' . $dir;
            dbg("+ setup check $fdir");
            if (!file_exists($fdir)) {
                mkdir($fdir);
            }

            if (!is_dir($fdir)) {
                throw new Exception("Could not create directory $fdir. Please make it a writeable directory.");
            }
            if (!is_writable($fdir)) {
                throw new Exception("Directory $fdir is not writeable.");
            }
        }
        return true;
    }
}
