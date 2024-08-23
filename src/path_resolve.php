<?php

namespace slowfoot;

const CURRENT_DIRECTORY = '.';
const PREVIOUS_DIRECTORY = '..';

class path_resolve {

    static public function resolve(string $path, array|string $base = ""): string {
        if (is_array($base)) {
            $base = join(DIRECTORY_SEPARATOR, $base);
        }
        $basePath = trim($base);
        $path = trim($path);

        if ('' === $path) {
            return $basePath;
        }

        $path = self::resolvePath($basePath . $path);
        $path = rtrim($path, "/");
        return $path;
    }

    static private function resolvePath(string $path): string {
        $resolvedPathParts = [];
        $parts = explode(DIRECTORY_SEPARATOR, $path);

        foreach ($parts as $part) {
            $part = trim($part);

            if ('' === $part || CURRENT_DIRECTORY === $part) {
                continue;
            }

            if (PREVIOUS_DIRECTORY !== $part) {
                array_push($resolvedPathParts, $part);
            } elseif (count($resolvedPathParts) > 0) {
                array_pop($resolvedPathParts);
            }
        }

        return DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $resolvedPathParts);
    }
}
