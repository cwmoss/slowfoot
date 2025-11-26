<?php

namespace slowfoot\util;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use LucidFrame\Console\ConsoleTable;

class console {

    static public function console($mode = "auto") {
        static $console_fn;
        static $console = new ConsoleColor();

        $presets = [
            'bold' => ['bold'],
            'reverse' => ['reverse'],
            'green' => ['color_28'],
        ];
        if (!$console_fn) {

            $styles = [];
            $colors = false;
            if ($console->isSupported()) {
                $styles = $console->getPossibleStyles();
            }
            if ($console->are256ColorsSupported()) {
                $colors = true;
            }
            $console_fn = function ($name, $text) use ($console, $presets, $mode) {
                if (PHP_SAPI != 'cli') {
                    #    return;
                }
                if ($mode == "off") return $text;
                $preset = $presets[$name] ?? null;
                if (!$preset) {
                    return $text;
                } else {
                    if ($name == 'reverse') {
                        $text = ' ' . $text . ' ';
                    }

                    return $console->apply($preset, $text);
                }
            };
        }

        if ($mode == "on") $console->setForceStyle(true);
        return $console_fn;
    }

    static public function console_table($header, $rows) {
        $table = new ConsoleTable();
        foreach ($header as $head) {
            $table->addHeader($head);
        }
        foreach ($rows as $row) {
            $table->addRow();
            foreach ($header as $name => $h) {
                $table->addColumn($row[$name]);
            }
        }
        return $table->getTable();
    }
}
