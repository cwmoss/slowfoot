<?php
/*

composer install sanity/sanity-php

            'dataset' => 'production',
            'projectId' => 'pna8s3iv', #$_ENV['SANITY_ID'],
            'useCdn' => false,
            'token' => $_ENV['SANITY_TOKEN'],
            // 'query' => '*[_type=="custom-type-query"]'
*/

namespace slowfoot_plugin\sanity;

use Sanity\BlockContent;
use Sanity\Client as SanityClient;
use slowfoot\configuration;
use slowfoot\hook;
use slowfoot\image\asset;
use slowfoot\store;

use function http_build_query;

class sanity {

    public function __construct(
        public string $project_id,
        public string $token = '',
        public string $dataset = 'production',
        public bool $use_cdn = false,
        public string $query = '*[!(_type match "system.*") && !(_id in path("drafts.**"))]'
    ) {
    }

    public function init() {
        hook::add('bind_template_helper', function ($ds, $src, $config) {
            dbg("++ bind sanity text helper", $ds);
            return ['sanity_text' => function ($text, $opts = []) use ($ds, $config) {
                //print_r($sl);
                return self::sanity_text($text, $opts, $ds, $config, $this);
            }];
        });
        hook::add_filter('assets_map', function ($img, store $store) {
            // sanity images
            if (isset($img->asset) && $img->asset->_ref) {
                //if ($img->_type == 'sanity.imageAsset') {
                return self::sanity_imagefield_to_slft($img, $store);
            }
            return $img;
        });
    }

    static public function data_loader(configuration $config) {
        $me = $config->get_plugin(self::class);
        $client = $me->get_client();
        $query = $me->query;
        #print "\n".$query."\n";
        $res = $client->fetch($query);
        // falls mehrteilige query => flach machen
        if ($res && !isset($res[0])) {
            return array_merge(...array_values($res));
        }

        return array_values($res);
    }

    public function load_preview_object($id, $type, configuration $config) {
        // print_r($config);
        //print_r(apache_request_headers());
        //print_r($_COOKIE);
        $me = $config->get_plugin(self::class);
        $client = $me->get_client();
        //       $client = sanity_client($config['preview']['sanity']);

        $document = $client->getDocument($id);
        //print_r($document);
        return $document;
        return [
            '_id' => $id,
            '_type' => 'artist',
            'title' => 'hoho',
            'firstname' => 'HEiko',
            'familyname' => 'van Gogh',
        ];
    }

    public function get_client() {
        return new SanityClient([
            'projectId' => $this->project_id,
            'dataset' => $this->dataset,
            'useCdn' => $this->use_cdn,
            'token' => $this->token
        ]);
    }

    static public function sanity_text($block, $opts, store $ds, configuration $config, self $plugin) {
        if (!$block) return "";
        #var_dump($conf);
        $serializer = hook::invoke_filter('sanity.block_serializers', [], $opts, $ds, $config);
        #var_dump($serializer);

        $html = BlockContent::toHtml($block, [
            'projectId' => $plugin->project_id,
            'dataset' => $plugin->dataset,
            'serializers' => $serializer,
        ]);
        return nl2br($html);
    }

    /*
    a named image type or direct image type consists of a field named asset,
      that is a reference to the document._type "sanity.imageAsset"
    */
    static public function sanity_imagefield_to_slft(object $img, store $store): asset {
        dbg("##### sanity imagefield to asset #####", $img);
        $sanity_asset = (object) $store->ref($img->asset);
        dbg($sanity_asset);
        return self::sanity_image_to_asset($sanity_asset);
    }

    static public function sanity_image_to_asset(object $sanity_asset) {
        $asset = new asset(
            _id: $sanity_asset->_id,
            _src: $sanity_asset->_src,
            url: $sanity_asset->url,
            path: $sanity_asset->path,
            w: $sanity_asset->metadata->dimensions->width,
            h: $sanity_asset->metadata->dimensions->height,
            mime: $sanity_asset->mimeType
        );

        if (isset($img->hotspot)) {
            $asset->fp = [$img->hotspot->x, $img->hotspot->y];
        }
        return $asset;
    }

    static public function sanity_resize($img, $opts) {
        // print_r($opts);
        $params = ['q' => 90];
        if ($opts['w']) {
            $params['w'] = $opts['w'];
        }
        if ($opts['h']) {
            $params['h'] = $opts['h'];
        }
        return $img['url'] . '?' . http_build_query($params);
    }

    /*
    works only with a specific schema:
      object with fields:
        - internal (reference)
        - route (string) destination path
        - external (string) complete url with host
  */
    static public function sanity_link_url($link, $ds) {
        // var_dump($link);
        return $link['internal'] ? $ds->get_path($link['internal']['_ref']) : ($link['route'] ? path_page($link['route']) : $link['external']);
    }
}
