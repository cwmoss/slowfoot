<?php

namespace slowfoot_plugin\markdown;

use slowfoot\hook;
use slowfoot\configuration;

use Mni\FrontYAML\Parser;
use ParsedownToc;
use ParsedownExtended;

class markdown_plugin {

    public shortcode $shortcode;

    public function init() {
        dbg("+++ hook shortcodes", hook::invoke_filter("markdown.shortcodes", []));

        hook::add('bind_template_helper', function ($ds, $src, configuration $config) {
            return ['markdown', $this->markdown_helper($config, $ds)];
        });

        hook::add('bind_late_template_helper', function ($helper, $base, $data) {
            dbg("++ late MD", $helper, $base, $data);
            $md = $helper['markdown'];
            return ['markdown', $this->markdown_helper_with_obj($md, $data)];
        });

        $this->shortcode = new shortcode(
            hook::invoke_filter("markdown.shortcodes", [])
        );
    }

    public function markdown_sf($md, $config = null, $ds = null) {
        if (!$md) return "";
        $parser = new markdown_sfp();
        $parser->setSetting('toc', true);
        $parser->set_context(['conf' => $config, 'ds' => $ds]);
        //$parser->setUrlsLinked(false);
        return $parser->text($md);
    }

    // TODO: no double parse
    static public function markdown_toc($content) {
        $parser = new ParsedownExtended();
        $parser->setSetting('toc', true);
        $body = $parser->body($content);
        $toc  = $parser->contentsList();
        dbg("+++ toc", $toc);
        return $toc;
    }

    public function markdown_parser($config, $ds) {
        $parser = new markdown_sfp();
        $parser->setSetting('toc', true);
        $parser->set_context(['conf' => $config, 'ds' => $ds]);
        $parser->set_shortcodes($this->shortcode);
        return $parser;
    }

    public function markdown_helper($config = null, $ds = null) {
        dbg("+++ markdown helper vanilla");
        $parser = $this->markdown_parser($config, $ds);

        return function ($text, $obj = null) use ($parser) {
            if (!$text) return "";
            dbg("vanilla helper", $obj, $parser->current_obj);
            $parser->set_current_obj($obj);
            return $parser->text($text);
        };
    }

    public function markdown_helper_with_obj($parser, $data = null) {
        dbg("+++ markdown helper object", $data, func_get_args());
        return function ($text) use ($parser, $data) {
            dbg("+++ call MD w object");
            return $parser($text, $data);
        };
    }
}
