<?php
require_once __DIR__."/../vendor/autoload.php";
/*
    http://userguide.icu-project.org/transforms/general
    https://stackoverflow.com/questions/3542717/how-to-remove-accents-and-turn-letters-into-plain-ascii-characters/3542748#3542748
    https://de.wikipedia.org/wiki/Normalisierung_(Unicode)
    https://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
    
    https://github.com/cocur/slugify
    **  https://github.com/ausi/slug-generator

    "ausi/slug-generator": "^1",
    "jbroadway/urlify": "^1",
*/

use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;

$opts = new SlugOptions()->setLocale("de");
$generator = new SlugGenerator($opts);

$tests = [
    'Hello Wörld!',
    'Καλημέρα',
    'фильм',
    '富士山',
    '國語',
    'Äpfel und Bäume',
    '  Haste mal  2€? ',
    "A æ Übérschall øve på høyeste nivå! И я люблю PHP! есть. ﬁ"
];

$max = 10000;
foreach(range(1, $max) as $round){
    foreach($tests as $t){
        $generator->generate($t);
        if($round==1) printf("%s: %s\n", $t, $generator->generate($t));
    }
}


