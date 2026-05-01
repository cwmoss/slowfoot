<?php

namespace slowfoot\store;

use Closure;
use phuety\exception;
use SQLite3;
use SQLite3Result;

/*
    old functions
    run($ddl_s)
    run("select body from docs WHERE _type=?", $type)
    safeQuery($q, $params)
    getPdo
    cell($q_count)
    cell('SELECT count(_id) from docs WHERE _id=?', $id)
    insert('docs', [
            'body' => \json_encode($row),
        ]);
    update('docs', [
            'body' => \json_encode($row),
        ], [
            '_id' => $id
        ]);
    row

    Pdo: 
    $pdo->createFunction($name, $fn, 1);

    notes:
        $db->busyTimeout(5000);
*/

class sqlite_driver {

    public SQLite3 $db;

    public function __construct(public string $db_file, public array $connect_options = []) {
        $this->connect();
    }

    public function connect() {
        $this->db = new SQLite3($this->db_file, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    }

    public function row(string $query, mixed ...$params): array {
        $res = $this->execute_query($query, $params);
        if (!$res) return [];
        return $res->fetchArray(SQLITE3_ASSOC) ?: [];
    }

    public function cell(string $query, mixed ...$params): mixed {
        $res = $this->execute_query($query, $params);
        if (!$res) return [];
        $row = $res->fetchArray(SQLITE3_NUM);
        if ($row === false) return null;
        return $row[0];
    }

    public function run(string $query, mixed ...$params) {
        return $this->safeQuery($query, $params);
    }

    // https://www.php.net/manual/en/sqlite3stmt.bindvalue.php
    public function safeQuery(string $query, array $params = []): array {
        $result = $this->execute_query($query, $params);
        if (!$result) return [];
        // php >= 8.5
        if(method_exists($result, "fetchAll"))
            return $result->fetchAll(SQLITE3_ASSOC)?:[];
        
        $res = [];
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $res[]=$row;
        }
        return $res;
    }

    public function execute_query(string $query, array $params): SQLite3Result|false {
        $stmt = $this->db->prepare($query);
        if ($stmt === false) {
            throw new exception("wrong SQL $query");
        }
        foreach ($params as $idx => $p) {
            // TODO: type?
            $stmt->bindValue($idx + 1, $p);
        }
        return $stmt->execute();
    }

    public function run_ddl(string $statements): bool {
        return $this->db->exec($statements);
    }

    public function insert(string $table, array $params) {
        $query = sprintf("INSERT INTO %s (%s) VALUES(%s)", $table, join(", ", array_keys($params)), join(", ", array_fill(0, count($params), '?')));
        $this->execute_query($query, array_values($params));
    }

    public function update(string $table, array $set, array $condition) {
        $set_keys = [];
        $cond = [];
        $params = [];
        foreach ($set as $k => $v) {
            $set_keys[] = "$k = ?";
            $params[] = $v;
        }
        foreach ($condition as $k => $v) {
            $cond[] = "$k = ?";
            $params[] = $v;
        }
        $query = sprintf("UPDATE %s SET %s WHERE %s", $table, join(", ", $set_keys), join(" AND ", $cond));
        $this->execute_query($query, array_values($params));
    }

    public function create_function(string $name, Closure $fn, int $arg_count) {
        $this->db->createFunction($name, $fn, $arg_count);
    }
}
