<?php

namespace slowfoot\store;

use cwmoss\lolql\lolql;
use slowfoot\databucket;

// use function lolql\query as lquery;

class memory extends store {
    public $docs = [];

    // key: _id, value: [path_name => path]
    public $paths = [];
    // key: path, value: [_id, path_name]
    public $paths_rev = [];

    public function has_data_on_create(): bool {
        return false;
    }

    public function transaction_start() {
    }
    public function transaction_end() {
    }

    public function query_sql(string $q, array $params = []) {
        return [];
    }

    public function query_fts(string $q): array {
        return [];
    }

    public function query_paginated_fn(string $q, int $limit_per_page = 20, array $params = []): array {
        $all = new lolql($q)->run($this->docs, $params);
        $total = count($all);
        $page_query = function ($page) use ($all, $limit_per_page) {
            $offset = ($page - 1) * $limit_per_page;
            $res = array_slice($all, $offset, $limit_per_page);
            return $res;
        };

        return [$total, $page_query];
    }

    private function result_map(array $res): array {
        return array_map(fn($it) => databucket::array_to_object($it), $res);
    }

    public function query_one(string $q, array $params = []): ?array {
        $res = new lolql($q)->limit_one()->run($this->docs, $params);
        $res = $this->result_map($res);
        return $res[0] ?? null;
    }

    public function query(string $q, $params = []): array {
        $res = new lolql($q)->run($this->docs, $params);
        // $res = lquery($this->docs, $q, $params);
        return $this->result_map($res);
        // return [$res, count($res)];
    }

    public function query_type(string $type, $page = 1, $limit = 1000): array {
        // TODO: page/limit
        $res = array_filter($this->docs, function ($row) use ($type) {
            return $row["_type"] == $type;
        });
        return $this->result_map($res);
    }

    public function exists(string $id): bool {
        return isset($this->docs[$id]);
    }

    public function get_doc(string $id): null|array|object {
        return $this->exists($id) ? databucket::array_to_object($this->docs[$id]) : null;
    }

    public function add_doc(string $id, array $row): bool {
        $this->docs[$id] = $row;
        return true;
    }

    public function update_doc(string $id, array $row): bool {
        $this->docs[$id] = $row;
        return true;
    }

    public function add_reference(string $src_id, string $src_prop, string $dest): bool {
        $this->docs[$src_id][$src_prop][] = ['_ref' => $dest];
        return true;
    }

    public function path_exists(string $path): bool {
        return isset($this->paths_rev[$path]);
    }

    public function path_add(string $path, string $id, string $name): bool {
        $this->paths[$id][$name] = $path;
        $this->paths_rev[$path] = [$id, $name];
        return true;
    }

    public function path_get(string $id, string $name): ?string {
        return $this->paths[$id][$name] ?? null;
    }

    public function path_get_all(string $id): array {
        return $this->paths[$id] ?? [];
    }

    public function path_update(string $old_path, string $id, string $name, string $new_path): bool {
        if (!$name) $name = "_";
        $this->paths[$id][$name] = $new_path;
        $this->paths_rev[$new_path] = [$id, $name];
        // unset($this->paths_rev[$old_path]);
        return true;
    }

    public function path_get_first(): ?array {
        $first = array_key_first($this->paths_rev);
        if (!$first) return null;
        return $this->path_get_by_path($first);
    }

    public function path_get_by_path(string $path): ?array {
        $p = $this->paths_rev[$path] ?? null;
        if (!$p) return $p;
        $p[] = $path;
        return $p;
    }

    public function path_get_props(string $path): array {
        return $this->paths_rev[$path] ?? [null, null];
    }

    public function info(): array {
        $info = [];
        foreach ($this->docs as $doc) {
            if (!\key_exists($doc['_type'], $info)) {
                $info[$doc['_type']] = ['_type' => $doc['_type'], 'total' => 0];
            }
            $info[$doc['_type']]['total']++;
        }
        $paths = array_reduce($this->paths, function ($res, $item) {
            return $res + count($item);
        }, 0);
        $info[] = ['_type' => '__paths', 'total' => $paths];
        return array_values($info);
    }
}
