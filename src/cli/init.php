<?php

use slowfoot\setup;

$setup = (new setup(SLF_PROJECT_DIR));

shell_info("initializing new project in " . SLF_PROJECT_DIR);

$skipped = $setup->init("minimal");
shell_info();

if ($skipped) {
  shell_info("some files could not be created, because they are already there:", true);
  print_r($skipped);
}
