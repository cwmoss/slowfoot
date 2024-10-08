<?php

namespace slowfoot;

class store {

  // key: _id, value: [path_name => path]
  public $paths = [];
  // key: path, value: [_id, path_name]
  public $paths_rev = [];

  public $info = ['loaded' => [], 'rejected' => [], 'conflicts' => 0];
  /*
    templates config
  */
  public $config = [];
  public $conflicts = [];

  public $db;

  public function __construct($db, $config) {
    $this->db = $db;
    $this->config = $config;
  }

  public function has_data_on_create() {
    return $this->db->has_data_on_create();
  }

  public function query_sql($q, $params) {
    return $this->db->query_sql($q, $params);
  }

  public function query_paginated($q, $limit, $params = []) {
    [$total, $page_query] = $this->db->query_paginated($q, $limit, $params);
    $totalpages = ceil($total / $limit);
    $info = [
      'total' => $total,
      'totalpages' => $totalpages,
      'minpage' => max(1, $totalpages),
      'limit' => $limit,
    ];
    return [$info, $page_query];
  }

  public function query($q, $params = []) {
    return $this->db->query($q, $params);
  }

  public function query_one($q, $params = []) {
    return $this->db->query_one($q, $params);
  }

  public function query_type($type) {
    return array_values($this->db->query_type($type));
  }

  public function data() {
    return $this->db->data();
  }

  public function id_maybe_object_or_array(int|string|array|object $id, $propname = '_id') {
    if (is_object($id)) {
      $id = $id->$propname;
    } elseif (is_array($id)) {
      $id = $id[$propname];
    }
    return (string) $id;
  }

  public function get($id) {
    $id = $this->id_maybe_object_or_array($id);
    return $this->db->get('docs', $id);
  }

  public function ref($id) {
    if (!$id) return;
    $id = $this->id_maybe_object_or_array($id, '_ref');
    return $this->db->get('docs', $id);
  }

  public function add($id, $row) {
    if ($this->db->exists("docs", $id)) {
      return false;
    }
    dbg("+++ store add ", $id, $row);
    $row['_id'] = $id;
    $this->db->add("docs", $id, $row);
    $this->info['loaded'][$row['_type']] ??= 0;
    $this->info['loaded'][$row['_type']]++;
    $this->add_path($row);
    return true;
  }

  public function add_row($row) {
    return $this->add($row['_id'], $row);
  }

  public function update($id, $row) {
    if (!$this->db->exists("docs", $id)) {
      return false;
    }
    $row['_id'] = $id;
    return $this->db->update("docs", $id, $row);
  }

  public function update_row($row) {
    return $this->update($row['_id'], $row);
  }

  public function add_ref($src_id, $src_prop, $dest) {
    $src_id = $this->id_maybe_object_or_array($src_id);
    $dest = $this->id_maybe_object_or_array($dest);
    $this->db->add_ref($src_id, $src_prop, $dest);
  }

  public function add_path($row) {
    //print ' type: ' . $row['_type'];
    // only, if we have a template for the type
    if (!isset($this->config[$row['_type']])) {
      return;
    }
    foreach ($this->config[$row['_type']] as $name => $conf) {
      //print_r($conf);
      $path = $conf['path']($row);
      if ($this->db->path_exists($path)) {
        $this->conflict($path, $name, $row);
      } else {
        $this->db->path_add($path, $row['_id'], $name);
      }
    }
  }

  public function get_path($id, $name = null) {
    return PATH_PREFIX . $this->get_fpath($id, $name);
  }

  public function get_fpath(int|string|array|object $id, $name = null) {
    $id = $this->id_maybe_object_or_array($id);
    if (!$name) {
      $name = '_';
    }
    $path = $this->db->path_get($id, $name);
    if ($path == "/index") {
      $path = "/";
    }
    return $path;
  }

  public function get_by_path($path) {
    // $path = trim($path, "/");
    return $this->db->path_get_props($path);
  }

  public function rejected($type) {
    if (!isset($this->info['rejected'][$type])) {
      $this->info['rejected'][$type] = 1;
    } else {
      $this->info['rejected'][$type]++;
    }
  }

  public function info() {
    return $this->db->info();
  }

  private function conflict($path, $name, $row) {
    [$firstid, $firstname] = $this->get_by_path($path);
    $first = $this->get($firstid);

    $this->conflicts[] = [
      'path' => $path,
      'rev' => [$row['_id'], $name],
      'first' => [
        '_id' => $firstid,
        '_type' => $first['_type'],
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
