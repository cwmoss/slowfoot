<?php

use function lolql\query as lquery;
use slowfoot\hook;
use slowfoot\configuration;
use slowfoot\image\processor;
use slowfoot\store;

function add_template_helper($name, $fun) {
}

function load_template_helper(store $ds, $src, configuration $config) {
  if (file_exists($src . '/template_helper.php')) {
    $custom = require_once($src . '/template_helper.php');
  } else {
    $custom = [];
  }
  foreach (hook::invoke('bind_template_helper', [], $ds, $src, $config) as $hlp) {
    // $custom[$hlp[0]] = $hlp[1];
    $custom = array_merge($custom, $hlp);
  }
  #var_dump($custom);
  $default = [
    'path' => function ($oid, $name = null) use ($ds) {
      //print "-- $oid";
      return $ds->get_path($oid, $name);
    },
    'get' => function ($oid) use ($ds) {
      return $ds->get($oid);
    },
    'ref' => function ($oid) use ($ds) {
      return $ds->ref($oid);
    },
    'q' => function ($query_string, $params = []) use ($ds) {
      return $ds->query_sql($query_string, $params);
    },
    'query' => function ($q, $params = []) use ($ds) {
      return $ds->query($q, $params);
      //lquery($ds->data, $q);
    },
    'query1' => function ($q, $params = []) use ($ds) {
      return $ds->query_one($q, $params);
      //lquery($ds->data, $q);
    },
    'image' => function ($asset, $profile) use ($config) {
      $p = new processor($config->assets);
      return $p->run($asset, $profile);
    },
    'image_tag' => function ($asset, $profile, $tag = []) use ($config) {
      $p = new processor($config->assets);
      return $p->image_tag($asset, $profile, $tag);
    },
    'image_url' => function ($asset, $profile) use ($config) {
      $p = new processor($config->assets);
      return $p->image_url($asset, $profile);
    },
    'asset_from_file' => function ($path) use ($config) {
      $p = new processor($config->assets);
      return $p->asset_from_file($path);
    },
  ];
  return array_merge($default, $custom);
}

function h($str) {
  return htmlspecialchars($str);
}


function text_for($pattern, $vars = []) {
  $repl = [];
  foreach ($vars as $k => $v) {
    $repl['{' . strtolower($k) . '}'] = $v;
  }
  $txt = $pattern;
  $txt = str_replace(array_keys($repl), $repl, $txt);
  return $txt;
}
