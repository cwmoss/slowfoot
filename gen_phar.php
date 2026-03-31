<?php
// php -d phar.readonly=0 gen_phar.php
// ./spc micro:combine app.phar -O bp-api
// https://gnugat.github.io/2026/03/25/turn-you-php-app-into-a-standalone-binary.html
// 
// better set to php.ini phar.readonly = 0
ini_set("phar.readonly", 0);

$pharFile = 'app.phar';

// clean up
if (file_exists($pharFile)) {
    unlink($pharFile);
}
if (file_exists($pharFile . '.gz')) {
    unlink($pharFile . '.gz');
}

// create with alias "project.phar"
$phar = new Phar('app.phar', 0, 'app.phar');
// add all files in the project
$phar->buildFromDirectory(dirname(__FILE__) . '/', "!/src|vendor|bin|plugins|resources|ui|webdeploy/!");
$phar->setStub($phar->createDefaultStub('bin/slowfoot'));
$phar->compress(Phar::GZ);

echo "OK\n";
exit;

// create phar
$p = new Phar($pharFile);

// creating our library using whole directory  
$p->buildFromDirectory('./php');

// pointing main file which requires all classes  
$p->setDefaultStub('php/bin/api.php');

// plus - compressing it into gzip  
$p->compress(Phar::GZ);

echo "$pharFile successfully created";
