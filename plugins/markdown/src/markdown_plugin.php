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
        // dbg("+++ hook shortcodes", hook::invoke_filter("markdown.shortcodes", []));

        hook::add('bind_template_helper', function ($ds, $src, configuration $config) {
            return $this->markdown_helper($config, $ds);
        });

        hook::add('bind_late_template_helper', function ($helper, $base, $data) {
            dbg("++ late MD", $helper, $base, $data);
            $md = $helper['markdown'];
            return ['markdown' => $this->markdown_helper_with_obj($md, $data)];
        });

        $this->shortcode = new shortcode(
            hook::invoke_filter("markdown.shortcodes", [])
        );
    }

    /*
        direkt ausführen
    */
    public function markdown_sf($md, $config = null, $ds = null) {
        if (!$md) return "";
        $parser = $this->markdown_parser($config, $ds);
        return $parser->text($md);
    }

    public function markdown_parser($config, $ds) {
        $parser = new markdown_sfp();
        # return $parser;
        $parser->config()->set('toc', true);
        // $parser->setSetting('toc', true);
        $parser->config()->set('smartypants', false);
        # this extension eats chars
        $parser->config()->set("typographer", false);
        $parser->config()->set("abbreviations", true);
        $parser->config()->set("comments", true);

        /* $Parser->setSetting('smarty', [
            'substitutions' => [
                'left-double-quote' => '"', // Double bottom quote
                'right-double-quote' => '"', // Double top quote
                "ndash" => '–',
                "mdash" => '—',
                "left-single-quote" => "'",
                "right-single-quote" => "'",
                "left-angle-quote" => '«',
                "right-angle-quote" => '»',
                "ellipses" => "..."
            ],

            "enabled" => true
        ]);*/
        $parser->set_context(['conf' => $config, 'ds' => $ds]);
        $parser->set_shortcodes($this->shortcode);
        return $parser;
    }

    public function markdown_helper($config = null, $ds = null) {
        // dbg("+++ markdown helper vanilla");
        $parser = $this->markdown_parser($config, $ds);

        return [
            "markdown_parser" => $parser,
            "markdown" => function ($text, $obj = null) use ($parser) {
                if (!$text) return "";
                dbg("vanilla helper", $obj, $parser->current_obj);
                $parser->set_current_obj($obj);
                return $parser->text($text);
            },
            "markdown_toc" => function ($text, $obj = null) use ($config, $ds) {
                if (!$text) return "";
                // we need a new parser
                $parser = $this->markdown_parser($config, $ds);
                $parser->body($text);
                // dbg("+++ markdown TOC", $parser->contentsList());
                // die();
                return $parser->contentsList();
            },
        ];
    }

    public function markdown_helper_with_obj($parser, $data = null) {
        // dbg("+++ markdown helper object", $data, func_get_args());
        return function ($text) use ($parser, $data) {
            // dbg("+++ call MD w object");
            return $parser($text, $data);
        };
    }
}
