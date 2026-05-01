<?php

require_once __DIR__."/../vendor/autoload.php";

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
        URLify::slug($t, language: "de");
        if($round==1) printf("%s: %s\n", $t, URLify::slug($t, language: "de"));
    }
}