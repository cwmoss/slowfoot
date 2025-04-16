<?php

class abc {
    public static $name = "bineintest";
    function tc() {
        $a = function () {
            print self::$name;
        };
        $a();
    }
}

new abc()->tc();
