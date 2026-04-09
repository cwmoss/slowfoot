<?php

namespace slowfoot;

/*

    Special characters:

    * - Matches zero or more characters.
    ? - Matches exactly one character (any character).
    [...] - Matches one character from a group of characters. 
        If the first character is !, matches any character not in the group.
    {a,b,c} - Matches one string from a group of strings delimited 
        by a comma when the GLOB_BRACE flag is used.
    \ - Escapes the following character, except when the GLOB_NOESCAPE flag is used.

*/

class globstar {

    public function __construct(public string $pattern) {
    }

    public function each() {
    }
}
