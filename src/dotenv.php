<?php

namespace slowfoot;

use RuntimeException;

use function getcwd;

class dotenv {

    public function __construct(public string $directory = "", public string $env_filename = ".env") {
        if (!$directory) $this->directory = getcwd();
    }

    public function load() {
        $file = $this->directory . DIRECTORY_SEPARATOR . $this->env_filename;
        if (!file_exists($file)) return;
        if (!is_readable($file)) throw new RuntimeException("dotenv file not readable: {$this->env_filename}");
        $lines = file($file) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line) continue;
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
