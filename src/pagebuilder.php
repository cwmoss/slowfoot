<?php

namespace slowfoot;

use Generator;
use slowfoot\util\html;

class pagebuilder {

  public template_contract $engine;

  public function __construct(
    public configuration $config,
    public store $ds,
    public array $helper
  ) {
    $this->engine = $config->get_template_engine();
  }

  public function make_template(
    string $name, /* eigentlich type */
    context $context,
    ?string $obj_id = null,
    null|array|object $data = null,
    ?array $template_conf = null
  ): string {
    if (!$data) {
      $data = $this->ds->get($obj_id);
    } else {
      //var_dump($data);
      $obj_id = $data->_id;
    }
    $obj = document::new((array) $data);
    dbg("template object data", $data, $obj_id);
    if (is_null($template_conf)) {
      $template_conf = $this->config->templates[$obj->_type][$name];
    }

    // $template = $templates[$obj['_type']][$name]['template'];
    // $template = $this->template_name($this->config->templates, $obj->_type, $name);
    $template = $template_conf["template"];
    dbg('template', $template, $obj);
    $content = $this->engine->run(
      $template,
      [
        'page' => $obj,
        'path' => $this->ds->get_path($obj_id, $name),
        'template_config' => $template_conf, //TODO
        'path_name' => $name
      ],
      $this->helper,
      $context->with('template_type', 'template')
    );
    // debug_js("page", $obj);
    // debug_js("meta", html::collect_data('meta', true));
    return $content;
  }

  public function make_page_bulk(string $pagename, context $context): Generator {
    $context = $context->with('template_type', 'page');
    $pp = $this->engine->preprocess($pagename, $context->src);
    if ($page_query = ($pp['page-query'] ?? null)) {
      //var_dump($paginate);
      dbg('[page] query', $page_query);
      if ($page_query['paginate']) {
        [$info, $pagequery] = $this->ds->query_paginated(
          $page_query['__content'],
          $page_query['paginate'],
          []
        );

        foreach (range(1, $info['totalpages']) as $pagenr) {
          $qres = $pagequery($pagenr);
          $pagination = self::pagination($info, $pagenr ?: 1);
          $content = $this->engine->run_page(
            $pagename,
            ['page' => $qres, 'pagination' => $pagination],
            $this->helper,
            $context
          );
          $content = $this->engine->remove_tags($content, ['page-query']);
          yield ["content" => $content, "pagenr" => $pagenr];
        }
      } else {
        $pagination = [];
        $qres = $this->ds->query($page_query['__content']);
        if ($page_query["first"]) {
          // dbg("++ yes first match");
          $qres = $qres[0] ?? [];
        }
        // query_page($ds, $pagination_query, $pagenr);
        $content = $this->engine->run_page(
          $pagename,
          ['page' => $qres, 'pagination' => $pagination],
          $this->helper,
          $context
        );
        $content = $this->engine->remove_tags($content, ['page-query']);
        yield ["content" => $content, "pagenr" => null];
      }
    } else {
      $content = $this->engine->run_page(
        $pagename,
        [],
        $this->helper,
        $context
      );
      yield ["content" => $content, "pagenr" => null];
    }
  }
  public function make_page(
    string $pagename,
    int $pagenr,
    string $requestpath,
    context $context
  ): string {
    #$pagination_query = check_pagination($pagename, $src);
    $pp = $this->engine->preprocess($pagename, $context->src);
    dbg('page query', $pp);
    if ($page_query = ($pp['page-query'] ?? null)) {
      //var_dump($paginate);
      dbg('[page] query', $page_query);
      if ($page_query['paginate']) {
        [$info, $pagequery] = $this->ds->query_paginated(
          $page_query['__content'],
          $page_query['paginate'],
          []
        );

        //foreach (range(1, $pages) as $page) {
        $qres = $pagequery($pagenr);
        $pagination = self::pagination($info, $pagenr ?: 1);
        //}
      } else {
        $pagination = [];
        $qres = $this->ds->query($page_query['__content']);  // query_page($ds, $pagination_query, $pagenr);
        if ($page_query["first"]) {
          // dbg("++ yes first match");
          $qres = $qres[0] ?? [];
        }
      }

      #var_dump($qres);
      //print_r($coll);
      $content = $this->engine->run_page(
        $pagename,
        ['page' => $qres, 'pagination' => $pagination],
        $this->helper,
        $context->with('template_type', 'page')
      );
      $content = $this->engine->remove_tags($content, ['page-query']);

      // debug_js("page", $qres);
      // debug_js("meta", html::collect_data('meta', true));
    } else {
      $content = $this->engine->run_page(
        $requestpath,
        [],
        $this->helper,
        $context->with('template_type', 'page')
      );

      // debug_js("page", []);
      // debug_js("meta", html::collect_data('meta', true));
    }
    return $content;
  }


  public function template_name($tconfig, $type, $name) {
    return $tconfig[$type][$name]['template'];
  }

  // TODO: remove
  public function template_context($type, $context, $data, $ds, $config): array {
    $context['template_type'] = $type;
    return hook::invoke_filter('modify_template_context', $context, $data, $ds, $config);
  }

  static public function pagination($info, $page) {
    return array_merge($info, [
      'page' => $page,
      'prev' => ($page - 1) ?: null,
      'next' => (($page + 1) <= $info['totalpages']) ? ($page + 1) : null
    ]);
  }
}
