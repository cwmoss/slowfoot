<?php

namespace slowfoot;

use cwmoss\final_cli\terminal as Final_cliTerminal;
use LucidFrame\Console\ConsoleTable;

class terminal extends Final_cliTerminal {

    public float $timer = 0;

    public function shell_info(?string $start = null, bool $single = false) {
        // last resort to non-cli stuff
        if (PHP_SAPI != 'cli' && PHP_SAPI != 'micro') {
            if (!(defined('SLOWFOOT_WEBDEPLOY') && SLOWFOOT_WEBDEPLOY)) {
                return;
            }
        }

        if ($start) {
            if (!$single) {
                $this->timer = microtime(true);
                $this->print("<b>$start</b> ... ");
            } else {
                $this->println("<green>$start</green>");
            }
        } else {
            $elapsed = microtime(true) - $this->timer;
            $this->println("<inv> " . nice_elapsed_time($elapsed)['print'] . " </inv>");
        }
    }

    public function console_table(array $header, array $rows) {
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
        $this->println($table->getTable());
    }
}
