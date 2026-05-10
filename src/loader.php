<?php

namespace slowfoot;

use slowfoot\store\store;
use slowfoot\store\sqlite;

class loader {

    public function __construct(public configuration $config) {
    }

    public function load(?terminal $terminal = null): store {
        $db = $this->config->get_store();
        $db_store = get_class($db->db);
        $is_db = $db_store == sqlite::class;
        $onload = $this->config->hooks['on_load'] ?? null;
        # TODO fetch or not
        if ($db->has_data_on_create()) {
            $terminal?->shell_info("store {$db_store} using old data", true);
            return $db;
        }

        if ($is_db) {
            $db->db->db->run_ddl("BEGIN");
        }
        $terminal?->shell_info("fetching data {$db_store}", true);

        foreach ($this->config->sources as $name => $loader) {
            $opts = ['loader' => $name, 'type' => $name, 'name' => $name];

            $fun = $loader(...);

            $terminal?->shell_info("fetching $name");

            foreach ($fun($this->config, $db) as $row) {
                if (!$row) continue;

                if ($this->config->is_prod && isset($row["_draft"]) && $row["_draft"]) {
                    continue;
                }
                if (!isset($row['_type']) || !$row['_type']) {
                    #print_r($row);
                    $row['_type'] = $opts['type'];
                }
                $otype = $row['_type'];
                $row['_src'] = $name;
                if ($onload) {
                    $row = $onload($row, $db, $this->config);
                }
                if (!$row) {
                    $db->rejected($otype);
                    continue;
                }
                $id = $row["_id"] ?? $row["id"] ?? null;
                if (!$id) {
                    $db->rejected($otype);
                    continue;
                }
                $row['_id'] = $id;
                $db->add($row['_id'], $row);
            }
            $terminal?->shell_info();
        }
        if ($is_db) {
            $db->db->db->run_ddl("COMMIT");
        }
        return $db;
    }
}
