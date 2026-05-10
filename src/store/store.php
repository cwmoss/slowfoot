<?php

namespace slowfoot\store;

use Generator;

/*
    start: 238 mem: 140 sqlite: 346
            236 145 341
*/

abstract class store {

    public array $stats = [];
    public array $info = ['loaded' => [], 'rejected' => [], 'conflicts' => 0];
    /*
    templates config
  */
    public array $path_config = [];
    public array $conflicts = [];

    abstract public function has_data_on_create(): bool;
    abstract public function info(): array;

    abstract public function transaction_start();
    abstract public function transaction_end();

    abstract public function get_doc(string $id): null|array|object;
    abstract public function exists(string $id): bool;
    abstract public function add_doc(string $id, array $row): bool;
    abstract public function update_doc(string $id, array $row): bool;
    abstract public function add_reference(string $src_id, string $src_prop, string $dest): bool;

    abstract public function query_sql(string $q, array $params = []);
    abstract public function query_fts(string $q): array;
    abstract public function query_paginated_fn(string $q, int $limit = 20, array $params = []): array;
    abstract public function query(string $q, array $params = []): array;
    abstract public function query_one(string $q, array $params = []): ?array;
    abstract public function query_type(string $type, $page = 1, $limit = 1000): array;

    abstract public function path_exists(string $path): bool;
    abstract public function path_add(string $path, string $id, string $name): bool;
    abstract public function path_get(string $id, string $name): ?string;
    abstract public function path_get_all(string $id): array;
    abstract public function path_get_by_path(string $path): ?array;
    abstract public function path_get_first(): ?array;
    abstract public function path_get_props(string $path): array;
    abstract public function path_update(string $old_path, string $id, string $name, string $new_path): bool;

    public function query_paginated(string $q, int $limit = 20, array $params = []) {
        [$total, $page_query] = $this->query_paginated_fn($q, $limit, $params);
        $totalpages = ceil($total / $limit);
        $info = [
            'total' => $total,
            'totalpages' => $totalpages,
            'minpage' => max(1, $totalpages),
            'limit' => $limit,
        ];
        return [$info, $page_query];
    }

    public function query_type_chunked(string $type, int $size = 1000): Generator {
        $info = $this->info();
        $total = 0;
        foreach ($info as $i) {
            if ($i["_type"] == $type) {
                $total = $i["total"];
                break;
            }
        }
        $pages = ceil($total / $size);
        foreach (range(1, $pages) as $page) {
            foreach ($this->query_type($type, $page, $size) as $doc) {
                yield $doc;
            }
        }
    }

    public function id_maybe_object_or_array(int|string|array|object $id, $propname = '_id'): string {
        if (is_object($id)) {
            $id = $id->$propname;
        } elseif (is_array($id)) {
            $id = $id[$propname];
        }
        return (string) $id;
    }

    public function get(int|string|array|object $id) {
        $id = $this->id_maybe_object_or_array($id);
        return $this->get_doc($id);
    }

    public function ref(int|string|array|object $id): null|array|object {
        if (!$id) return [];
        $id = $this->id_maybe_object_or_array($id, '_ref');
        return $this->get_doc($id);
    }

    public function add(string $id, array|object $row): bool {
        if ($this->exists($id)) {
            return false;
        }
        dbg("+++ store add ", $id, $row);
        $row['_id'] = $id;
        $this->add_doc($id, $row);
        $this->info['loaded'][$row['_type']] ??= 0;
        $this->info['loaded'][$row['_type']]++;
        $this->add_path($row);
        return true;
    }

    public function add_row(array $row): bool {
        return $this->add($row['_id'], $row);
    }

    public function update(string $id, array $row): bool {
        if (!$this->exists($id)) {
            return false;
        }
        $row['_id'] = $id;
        return $this->update_doc($id, $row);
    }

    public function update_row(array $row): bool {
        return $this->update($row['_id'], $row);
    }

    public function add_ref(int|string|array|object $src_id, string $src_prop, int|string|array|object $dest): bool {
        $src_id = $this->id_maybe_object_or_array($src_id);
        $dest = $this->id_maybe_object_or_array($dest);
        return $this->add_reference($src_id, $src_prop, $dest);
    }

    public function add_path(array|object $row) {
        //print ' type: ' . $row['_type'];
        // only, if we have a template for the type
        if (!isset($this->path_config[$row['_type']])) {
            return;
        }
        foreach ($this->path_config[$row['_type']] as $name => $conf) {
            //print_r($conf);
            if (isset($row['_no_path']) && $row['_no_path']) continue;

            $path = $conf['path']($row);
            if ($path === null) continue;

            if ($this->path_exists($path)) {
                $this->conflict($path, $name, $row);
            } else {
                $this->path_add($path, $row['_id'], $name);
            }
        }
    }

    // TODO: be more strict
    public function get_path(int|string|array|object|null $id, ?string $name = null): ?string {
        if (!$id) return null;
        $p = $this->get_fpath($id, $name);
        if ($p === null) return null;
        return PATH_PREFIX . $p;
    }

    public function get_fpath(int|string|array|object|null $id, ?string $name = null): ?string {
        if (!$id) return null;
        $id = $this->id_maybe_object_or_array($id);
        if (!$name) {
            $name = '_';
        }
        $path = $this->path_get($id, $name);
        if ($path === null) return null;
        if ($path == "/index") {
            $path = "/";
        }
        return $path;
    }

    public function get_all_paths(int|string|array|object $id): array {
        $id = $this->id_maybe_object_or_array($id);
        return $this->path_get_all($id);
    }

    public function find_or_select_startpage(): ?array {
        $found = $this->path_get_by_path("/index");
        if ($found) return $found;
        $tests = ["/readme", "/start", "/home"];
        foreach ($tests as $test) {
            $found = $this->path_get_by_path($test);
            if ($found) break;
        }
        if (!$found) $found = $this->path_get_first();
        if ($found) {
            $this->path_update($found[2], $found[0], $found[1], "/index");
            return $found;
        }
        return null;
    }

    public function get_by_path(string $path): array {
        // $path = trim($path, "/");
        return $this->path_get_props($path);
    }

    public function rejected(string $type) {
        if (!isset($this->info['rejected'][$type])) {
            $this->info['rejected'][$type] = 1;
        } else {
            $this->info['rejected'][$type]++;
        }
    }

    private function conflict(string $path, string $name, array $row) {
        [$firstid, $firstname] = $this->get_by_path($path);
        $first = $this->get($firstid);

        $this->conflicts[] = [
            'path' => $path,
            'rev' => [$row['_id'], $name],
            'first' => [
                '_id' => $firstid,
                '_type' => $first->_type,
                'name' => $firstname,
                'row' => $first
            ],
            'second' => [
                '_id' => $row['_id'],
                '_type' => $row['_type'],
                'name' => $name,
                'row' => $row
            ]
        ];
        $this->info['conflicts']++;
    }
}
