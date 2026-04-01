<?php
// php -d phar.readonly=0 gen_phar.php
// ./spc micro:combine app.phar -O bp-api
// https://gnugat.github.io/2026/03/25/turn-you-php-app-into-a-standalone-binary.html
// 
// better set to php.ini phar.readonly = 0
// https://stackoverflow.com/questions/20264737/php-list-directory-structure-and-exclude-some-directories

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

/*

// Source - https://stackoverflow.com/a/67459883
// Posted by Miloš Đakonović
// Retrieved 2026-04-01, License - CC BY-SA 4.0

$exclude = ['file0.txt', 'file1.txt', 'file2.txt'];
*/
/**
 * @param SplFileInfo $file
 * @param mixed $key
 * @param RecursiveCallbackFilterIterator $iterator
 * @return bool True if you need to recurse or if the item is acceptable
 */
/*
$filter = function ($file, $key, $iterator) use ($exclude) {
    foreach($exclude as $excludefilename){
        if(
            strcmp(__DIR__ . DIRECTORY_SEPARATOR . $excludefilename, $file) === 0
        ) return false;
    }
    return true;
};

$innerIterator = new RecursiveDirectoryIterator(
    __DIR__,
    RecursiveDirectoryIterator::SKIP_DOTS
);
$iterator = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator($innerIterator, $filter)
);

$phar = new PharData('project.tar');
$phar->buildFromIterator($iterator, __DIR__);
*/
