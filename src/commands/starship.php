<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use slowfoot\store\sqlite;

class starship {

    /**
     * generates info for starship.
     * 
     */
    public function __invoke(
        #[cli("-d", "Set the project base directory")]
        ?string $project_directory = null,
        bool $webdeploy = false,
        bool $force = false
    ) {
        $boot_only_config = true;
        $boot_quiet = true;
        print "🌈 slft";
        $dir = getcwd();
        if (file_exists($dir . "/var/slowfoot.db")) {
            $db = new sqlite(["adapter" => "sqlite:" . $dir . "/var/slowfoot.db"]);
            $info = $db->info_line();
            printf(" %s docs, %s paths", $info[0], $info[1]);
        } else {
            print " no db";
        }
    }
}
