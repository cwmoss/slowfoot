<?php

namespace slowfoot_plugin\markdown;

use ParsedownToc;
use ParsedownExtended;
use slowfoot\image;

/*
https://github.com/erusev/parsedown/wiki/Tutorial:-Create-Extensions#change-element-markup

https://github.com/Nessworthy/parsedown-extension-manager

*/

class markdown_sfp extends ParsedownExtended {
    public $context;
    public $current_obj;

    protected $shortcodes;

    public function set_context($ctx) {
        $this->context = $ctx;
    }

    public function set_current_obj($obj) {
        $this->current_obj = $obj;
    }

    public function set_shortcodes(shortcode $shortcodes) {
        $this->InlineTypes['['][] = 'Shortcode';
        $this->shortcodes = $shortcodes;
        //$this->addInlineType('[', "Shortcode");
        // $this->inlineMarkerList .= '^';
    }

    # TODO:
    #   - check protocol
    #   - check images
    protected function inlineLink($Excerpt) {
        $link = parent::inlineLink($Excerpt);
        if (is_array($link)) {
            $href = $link['element']['attributes']['href'];
            $href = $this->resolve_link($href);
            $link['element']['attributes']['href'] = $href;
        }
        dbg("++ final link", $link);
        return $link;
    }

    // wird auch für !image tags aufgerufen
    protected function resolve_link($href) {
        if ($href[0] == '/') {
            return $href;
        }
        #TODO: parse_url

        #$id = get_absolute_path(dirname($this->current_obj['page']['_id']).'/'.$href );
        $id = get_absolute_path_from_base(
            $href,
            dirname($this->current_obj['page']['_id']),
            $this->context['conf']->base
        );

        if (pathinfo($href, PATHINFO_EXTENSION)) {
            return $id;
        }
        $src_conf = $this->context['conf'];
        dbg("+++ id-> link", $href, $id);
        return $this->context['ds']->get_path($id);
        dbg("current obj", $this->current_obj);
        #return '--resolvedd--'.$this->current_obj['title'].$href;
        return '--resolved--' . $this->current_obj['page']['_file']['path'] . $href;
    }

    protected function resolve_image_path($imgpath) {

        $path = get_absolute_path_from_base(
            $imgpath,
            dirname($this->current_obj["page"]->_file["full"]),
            $this->context['conf']->base
        );
        dbg("image path", $this->context['conf']->base, $imgpath, $this->current_obj["page"]->_id, $path);
        return $path;
    }

    protected function inlineImage($Excerpt) {
        dbg("+++ img excerpt", $Excerpt);
        $img = parent::inlineImage($Excerpt);
        if (is_array($img)) {
            dbg("+++ markdown img", $img);
            $path = $this->resolve_image_path($img['element']['attributes']['src']);
            dbg("+++ markdown img path", $path);
            $pipe = $this->context['conf']->get_image_processor();
            $img_url = $pipe->image_url(
                $path,
                new image\profile, // empty size == copy only
            );
            $img['element']['attributes']['src'] = $img_url;
            $img['element']['attributes']['data-slft'] = 'ok';
        }
        return $img;
    }

    protected function inlineShortcode($Excerpt) {
        dbg("+++ shortcode excerpt", $Excerpt);

        $res = $this->shortcodes->resolve($Excerpt);
        if (!$res) return;

        $el = [
            'extent' => strlen($Excerpt['text']),
            'element' => array(
                "allowRawHtmlInSafeMode" => true,
                "rawHtml" => $res,
                'name' => 'div',
                // 'text' => $matches[1],
                'attributes' => array(
                    'shortcode' => true,
                ),
                'handler' => 'hdl_shortcode'
                /*array(
                    'function' => 'shortcode',
                    'argument' => 'hoho',
                    'destination' => 'rawHtml',
                )*/
            )
        ];
        dbg("++ return element", $el);
        return $el;
    }

    public function hdl_shortcode($html) {
        dbg("++++ handle shortcode", $html);
        return $html;
    }
}