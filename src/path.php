<?php

namespace slowfoot;

use Closure;

/*
    generates a function for data-ingest
    
*/

class path {

    public ?Closure $reject = null;

    public function __construct(public string $pattern) {
    }

    public function reject_if(Closure $fn): self {
        $this->reject = $fn;
        return $this;
    }

    public function make_function(): Closure {
        $pattern = $this->pattern;
        $reject = $this->reject;
        $replacements = [];
        if (preg_match_all('!:([^/:]+)!', $pattern, $mat, PREG_SET_ORDER)) {
            $replacements = $mat;
        }
        $replacements = array_map(fn($r) => [$r[0], explode('.', $r[1])], $replacements);
        // print_r($replacements);
        // exit;
        return static function ($item) use ($pattern, $replacements, $reject): ?string {
            if ($reject && $reject($item)) return null;
            $path = $pattern;
            // $item[$r[1]]
            $replacements = array_map(fn($r) => [$r[0], self::url_safe(self::resolve_dot_value($r[1], $item))], $replacements);
            $path = str_replace(
                array_column($replacements, 0),
                array_column($replacements, 1),
                $path
            );
            return $path;
        };
    }

    static public function resolve_dot_value($keys, $data) {
        if (!$data) {
            return null;
        }
        $current = array_shift($keys);

        // nested?
        if ($keys) {
            return self::resolve_dot_value($keys, $data[$current] ?? null);
        }
        if (is_object($data)) {
            return $data->$current ?? null;
        }
        if (!is_assoc($data)) {
            return array_column($data, $current);
        } else {
            return $data[$current] ?? null;
        }
    }
    static public function url_safe($path) {
        // TODO
        // https://gist.github.com/jaywilliams/119517
        $path = iconv('UTF-8', 'ASCII//TRANSLIT', $path);
        $path = str_replace(" ", "-", $path);
        $path = str_replace(['"', "'", "`"], "", $path);
        $path = strtolower($path);
        return $path;
    }
}
