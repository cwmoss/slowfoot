<?php

namespace slowfoot;
/*
 '_file' => [
                    'path' => $fname,
                    'full' => $fullname,
                    'dir' => ltrim($path_parts['dirname'], "./"),
                    'name' => $path_parts['filename'],
                    'ext' => $path_parts['extension']
                ]
                    */

class file_meta {

    public string $path;
    public string $dir;
    public string $name;
    public string $ext;
    public int $modified;
    public int $created;
    public int $size;
    public string $content;

    public function __construct(
        public string $full,
        private string $base,
        private string $current = "",
        private string $remove_prefix = ""
    ) {
        // fullpath already contains the basepath?
        if ($base && str_starts_with($full, $base)) {
            $this->full = substr($full, strlen($base));
        }
        $this->read();
        $this->path_info();
    }

    public function read() {
        $file = $this->base . "/" . $this->full;
        $this->content = file_get_contents($file);
        $this->size = filesize($file);
        $this->modified = filemtime($file);
        $this->created = filectime($file);
    }

    public function path_info() {
        $fname = $this->full;
        if ($this->remove_prefix) {
            $fname = preg_replace("~^{$this->remove_prefix}~", "", $this->full);
        }
        $this->path = $fname;
        $info = pathinfo($fname);
        $this->dir = ltrim($info["dirname"], "./");
        $this->ext = $info["extension"];
        $this->name = $info["filename"];
    }

    public function get_document($id = null, string $type = "file"): document {
        return new document($id ?? $this->get_id(), $type, ["_file" => $this, "content" => $this->content]);
    }

    public function get_id(): string {
        return ltrim($this->dir . '/' . $this->name, "/.");
    }

    public function get_content(): string {
        return $this->content;
    }
}
