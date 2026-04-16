<?php

namespace slowfoot;

use Exception;
use FilesystemIterator;

class setup {

    public function __construct(public string $project_dir, public string $writebase = "") {
    }

    public function setup(): bool {
        // TODO: inject somewhere
        $writebase = $this->writebase;;
        if ($writebase) $writebase = $this->project_dir . "/" . $writebase;
        else $writebase = $this->project_dir;

        $writeable = [
            'var',
            'var/download',
            'var/rendered-images',
            'var/template'
        ];

        foreach ($writeable as $dir) {
            $fdir = $writebase . '/' . $dir;
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

    public function init(string $projectname): array {
        $skipped = [];
        $projectbase = realpath($this->project_dir);
        print __DIR__ . "/../resources/projects/$projectname\n";
        // $srcbase = realpath(__DIR__ . "/../resources/projects/$projectname");
        $srcbase = __DIR__ . "/../resources/projects/$projectname";
        if (!$srcbase) throw new Exception("sourcebase for '$projectname' not found");

        // $iterator = new FilesystemIterator($srcbase);
        $iterator = recurse_directory($srcbase);
        foreach ($iterator as $file) {
            // if (!$file->isFile()) continue;
            $rel_name = \substr($file->getPathname(), mb_strlen($srcbase));

            // print "found: " . $file->getFilename() . " // $rel_name  => " . $file->getPathname() . "\n";
            $rel_dir = \dirname($rel_name);
            $rel_file = \basename($rel_name);
            if (str_starts_with($rel_file, "dot.")) {
                $rel_file = \substr($rel_file, 3);
            }
            $dest = \ltrim($rel_dir . "/" . $rel_file, "/");
            $destfile = $projectbase . "/" . $dest;
            if (file_exists($destfile)) {
                $skipped[] = $dest;
                continue;
            }
            if (!is_dir(dirname($destfile))) mkdir(dirname($destfile), recursive: true);
            copy($file, $destfile);
            // print $file . " => $dest => $destfile\n";
        }
        return $skipped;
    }

    public function webdeploy(): array {
        $skipped = [];
        $projectbase = realpath($this->project_dir);
        $sourcedir = __DIR__ . "/_main";
        $destdir = $projectbase . "/webdeploy";
        if (!is_dir($destdir)) mkdir($destdir);
        $files = ["webdeploy.php" => "index.php"];
        foreach ($files as $file_src => $file_dest) {
            $destfile = $destdir . "/" . $file_dest;
            if (file_exists($destfile)) {
                $skipped[] = "webdeploy/$file_dest";
                continue;
            }
            copy($sourcedir . "/" . $file_src, $destfile);
        }
        return $skipped;
    }
}
