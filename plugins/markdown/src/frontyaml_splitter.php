<?php

namespace slowfoot_plugin\markdown;

/**
 * YAML Front matter splitter
 * 
 * original work copied from https://github.com/mnapoli/FrontYAML
 * 
 * removed parser dependencies
 * left only splitter function
 */
class frontyaml_splitter {

    private array $startSep;

    private array $endSep;

    public function __construct(
        array|string $startSep = '---',
        array|string $endSep = '---'
    ) {
        $this->startSep = array_filter((array) $startSep, 'is_string') ?: ['---'];
        $this->endSep = array_filter((array) $endSep, 'is_string') ?: ['---'];
    }

    /**
     * Split a string containing the YAML front matter and the markdown.
     * 
     */
    public function split(string $str): array {
        $yaml = null;

        $quote = static function ($str) {
            return preg_quote($str, "~");
        };

        $regex = '~^('
            . implode('|', array_map($quote, $this->startSep)) # $matches[1] start separator
            . "){1}[\r\n|\n]*(.*?)[\r\n|\n]+("                       # $matches[2] between separators
            . implode('|', array_map($quote, $this->endSep))   # $matches[3] end separator
            . "){1}[\r\n|\n]*(.*)$~s";                               # $matches[4] document content

        if (preg_match($regex, $str, $matches) === 1) { // There is a Front matter
            $yaml = trim($matches[2]) !== '' ? $this->yamlParser->parse(trim($matches[2])) : null;
            $str = ltrim($matches[4]);
        }

        return [$yaml, $str];
    }
}
