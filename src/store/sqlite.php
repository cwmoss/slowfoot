<?php
/*

CREATE VIRTUAL TABLE docs_fts USING fts5( _id, btext);

INSERT INTO docs_fts(_id, btext)
    SELECT _id, group_concat(b.key || ': ' ||  b.value, x'0a') as btext from docs, json_tree(body) b where b.atom not null group by _id;

*/

namespace slowfoot\store;

use cwmoss\lolql\lolql;
use slowfoot\document;

class sqlite extends store {

    public bool $was_filled = false;
    public sqlite_driver $driver;

    public static bool $json_array_mode = false;

    public function __construct(array $config, bool $fresh_create = false) {
        $adapter = explode(':', $config['adapter']);
        $name = $adapter[1] ?? 'slowfoot.db';
        if ($name == 'memory') {
            $name = ':memory:';
        } else {
            if ($name[0] != '/') {
                $name = $config['base'] . '/' . $name;
            }
            if ($fresh_create) {
                dbg("removing DB file", $name);
                unlink($name);
            }
            $this->was_filled = \file_exists($name);
        }
        // dbg("+++ new sqlite", $name);
        $this->driver = new sqlite_driver($name); // self::make_easydb($name);
        $this->create_schema();
    }

    public function has_data_on_create(): bool {
        return $this->was_filled;
    }

    public function create_schema() {
        $ddl = "
CREATE TABLE IF NOT EXISTS docs (
    body JSON,
    _id TEXT GENERATED ALWAYS AS (json_extract(body, '$._id'))
        VIRTUAL
        NOT NULL
        UNIQUE ON CONFLICT REPLACE,
    _type TEXT GENERATED ALWAYS AS (json_extract(body, '$._type'))
        VIRTUAL
        NOT NULL
    );
CREATE INDEX IF NOT EXISTS docs_id on docs(_id);
CREATE INDEX IF NOT EXISTS docs_type on docs(_type);
CREATE TABLE IF NOT EXISTS paths (
    path TEXT NOT NULL,
    id TEXT NOT NULL,
    name TEXT DEFAULT '_' NOT NULL
    );
CREATE INDEX IF NOT EXISTS paths_path on paths(path);
CREATE INDEX IF NOT EXISTS paths_id on paths(id);
CREATE VIRTUAL TABLE IF NOT EXISTS docs_fts USING fts5( _id, _type, btext)

        ";
        $statements = explode(';', $ddl);
        #print $ddl;
        foreach ($statements as $ddl_s) {
            if (trim($ddl_s)) {
                $this->driver->run_ddl($ddl_s);
            }
        }
        return;
    }

    public function transaction_start() {
        $this->driver->run_ddl("BEGIN");
    }
    public function transaction_end() {
        $this->driver->run_ddl("COMMIT");
    }

    public function update_fts(array|object $doc) {
        $fts = flatten($doc);
        $this->driver->insert("docs_fts", ['_id' => $doc['_id'], '_type' => $doc['_type'], 'btext' => join("\n", $fts)]);
    }

    public function query_fts(string $q): array {
        $query = "SELECT _id, _type, snippet(docs_fts, 2, '<b>', '</b>', '[...]', 30) body 
        FROM docs_fts WHERE docs_fts = ? ";
        $res = $this->driver->safeQuery($query, [$q]);
        dbg("[sqlite] fts", $query, $q);
        return $res;
        /*
    return [[], 0];
    $res = lquery($this->docs, $q);
    return [$res, count($res)];
    */
    }

    public function query_sql(string $q, array $params = []) {
        $res = $this->driver->safeQuery($q, $params);
        dbg("[sqlite] query_sql", $q, $params);
        #var_dump($q);
        #var_dump($res);
        $res = array_map(function ($r) {
            return json_decode($r['body'], self::$json_array_mode);
        }, $res);
        return $res;
        /*
    return [[], 0];
    $res = lquery($this->docs, $q);
    return [$res, count($res)];
    */
    }

    public function query_paginated_fn(string $q, int $limit_per_page = 20, array $params = []): array {
        $lol = new lolql($q);
        $q = $lol->make_sql($params);
        // $this->driver->db->createFunction($q->fun_name, $q->fun, 1);
        $total = $this->driver->cell($q->count_sql());

        $page_query = function ($page) use ($q, $limit_per_page, $lol, $params) {
            $off = ($page - 1) * $limit_per_page;
            $q = $q->with_limit($limit_per_page, $off);
            // print "Q: $q\n";
            $res = $this->driver->run($q->sql, $params);
            $res = array_map(function ($r) {
                return json_decode($r['body'], self::$json_array_mode);
            }, $res);
            if ($lol->query->projection) {
                // TODO: doesn't work, since data is an object
                $res = array_map(fn($data) => $lol->query->projection->evaluate($data, $params), $res);
            }
            return $res;
        };

        return [$total, $page_query];
    }

    public function query_one(string $q, array $params = []): ?array {
        dbg("++ query1 sqlite", $q);
        $res = $this->query($q, $params, 1);
        dbg("++ query1 res", $res);
        return $res[0] ?? null;
    }

    public function query(string $q, array $params = [], int $limit = 0): array {
        dbg("== LOLQL query", $q, $params, $limit);
        $lol = new lolql($q);
        if ($limit) $lol->limit_one();

        $q = $lol->make_sql($params);

        // $this->driver->db->createFunction($q->fun_name, $q->fun, 1);

        $res = $this->driver->run($q->sql);
        $res = array_map(function ($r) {
            return json_decode($r['body'], self::$json_array_mode);
        }, $res);

        if ($lol->query->projection) {
            $res = array_map(fn($data) => $lol->query->projection->evaluate($data, $params), $res);
        }

        return $res;
    }

    // empty string or array
    public function build_order(array|string $o = []): string {
        if (!$o) {
            return "";
        }
        $sql = [];
        foreach ($o as $order) {
            $sql[] = $this->propname($order['k']) . ' ' . $order['d'];
        }
        return join(", ", $sql);
    }
    public function propname(string $n) {
        $name = sprintf(
            "json_extract(body, '\$.%s')",
            $n
        );
        return $name;
    }

    // TODO: limits
    public function query_type(string $type, $page = 1, $limit = 1000): array {
        $offset = ($page - 1) * $limit;
        $res = $this->driver->run("select body from docs WHERE _type=? LIMIT $limit OFFSET $offset", $type);
        $res = array_map(function ($r) {
            return json_decode($r['body'], self::$json_array_mode);
        }, $res);
        return $res;
        /*    $filter = ['_type' => $type];
    $rs = array_filter($this->docs, function ($row) use ($filter) {
      return evaluate($filter, $row);
    });
    return $rs;
*/
    }

    public function exists(string $id): bool {
        return $this->driver->cell('SELECT count(_id) from docs WHERE _id=?', $id) ? true : false;
    }

    public function get_doc(string $id): null|array|object {
        return $this->_select_one($id);
    }

    public function add_doc(string $id, array|document $row): bool {
        $this->driver->insert('docs', [
            'body' => \json_encode($row),
        ]);
        $this->update_fts($row);
        return true;
    }

    public function update_doc(string $id, array|document $row): bool {
        $this->driver->update('docs', [
            'body' => \json_encode($row),
        ], [
            '_id' => $id
        ]);
        return true;
    }

    public function add_reference(string $src_id, string $src_prop, string $dest): bool {
        //    $this->data[$src_id][$src_prop][] = ['_ref' => $dest];

        $row = $this->get_doc($src_id);
        dbg("add-ref", $src_id, $row);
        $row[$src_prop][] = ['_ref' => $dest];
        return $this->update_doc($row['_id'], $row);
    }

    public function path_exists(string $path): bool {
        return $this->driver->cell('SELECT count(id) from paths WHERE path=?', $path) ? true : false;
    }

    public function path_add(string $path, string $id, string $name): bool {
        $this->driver->insert('paths', [
            'path' => $path,
            'id' => $id,
            'name' => $name
        ]);
        return true;
    }

    public function path_update(string $old_path, string $id, string $name, string $new_path): bool {
        if (!$name) $name = "_";
        $this->driver->update('paths', [
            'path' => $new_path,
        ], [
            'path' => $old_path,
            'id' => $id,
            'name' => $name
        ]);
        // var_dump("path update", $affected, $old_path, $id, $name, $new_path);
        return true;
    }

    public function path_get(string $id, string $name): ?string {
        $p = $this->driver->cell('SELECT path from paths WHERE id=? AND name=?', $id, $name);
        return $p;
    }

    public function path_get_all(string $id): array {
        $p = $this->driver->run('SELECT path from paths WHERE id=?', $id);
        return $p;
    }

    public function path_get_first(): ?array {
        // TODO: use some sort criteria?
        $p = $this->driver->row('SELECT id, name, path from paths LIMIT 1');
        if ($p) return [$p['id'] ?? null, $p['name'] ?? null, $p['path'] ?? null];
        return null;
    }

    public function path_get_by_path(string $path): ?array {
        $p = $this->driver->row('SELECT id,name,path from paths WHERE path=?', $path);
        if ($p) {
            return array_values($p);
        }
        return $p ?: null;
    }

    public function path_get_props(string $path): array {
        $p = $this->driver->row('SELECT id,name from paths WHERE path=?', $path);
        return [$p['id'] ?? null, $p['name'] ?? null];
    }

    public function _select_one(string $id) {
        return json_decode($this->driver->cell('SELECT body from docs WHERE _id=?', $id), self::$json_array_mode);
    }

    public function info(): array {
        $types = $this->driver->run('SELECT _type, count(*) AS total FROM docs GROUP BY _type');
        $routes = $this->driver->run("SELECT '__paths' as _type, count(*) AS total FROM paths");
        return array_merge($types, $routes);
    }
    public function info_line() {
        $types = $this->driver->cell('SELECT count(*) AS total FROM docs');
        $routes = $this->driver->cell("SELECT count(*) AS total FROM paths");
        return [$types, $routes];
    }
}
