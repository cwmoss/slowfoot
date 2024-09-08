<?php

namespace slowfoot\image;

use Exception;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Filesystem;
use League\Glide\Server;
// use League\Flysystem\Filesystem\Cache;

use function getimagesize;
use function is_string;
use function in_array;
use function file_exists;
/*

    size
    500x400
    500x
    x400
    "" (empty) => only copy, don't resize

    mode
    - fit, fit image to max constraints => whole image visible
    - fill, crop if aspect-ratio needs to, use fp (focalpoint if available)

    cache: rendered-images
*/

class processor {

  static public Server $resizer;
  static public Filesystem $cache;

  public function __construct(public configuration $conf) {
    if (!isset(self::$resizer)) {
      self::get_resizer($conf->base);
    }
  }

  public function run(string|array|asset $img, profile|string $opts): ?asset {

    $profile = $this->get_profile($opts);

    // local images, die direkt genutzt werden
    if (is_string($img)) {
      $img = $this->asset_from_file($img);
      dbg("[image] asset from file", $img);
      if (!$img) {
        return null;
      }
    }

    if (is_array($img)) {
      $img = (object) $img;
    }
    if ($img->_type != 'slft.asset') {
      if (!$this->conf->map) {
        return null;
      }
      $fn = $this->conf->map;
      $img = $fn($img);
    }

    dbg("[image] start", $img, $profile, $opts);

    if (self::is_remote($img->url)) {
      $img->remote_src = true;

      if ($this->conf->download) {
        $dl_name = $img->_id;
        $download_file = $this->conf->base . '/var/download/' . $dl_name;

        dbg("[image] download to ", $download_file);

        $resd = self::download_remote_image($img->url, $download_file);
        if ($resd[0]) {
          $img->download_file = $download_file; #'/download/'.$dl_name;
        } else {
          return null;
        }
      } else {
        if ($this->conf->resize_cdn) {
          $fn = $this->conf->resize_cdn;
          $cdn_url = $fn($img, $profile);
          $img->resize_url = $cdn_url;
          dbg("[image] resize_cdn", $cdn_url);

          return $img;
        }
      }
    }

    $name = $this->get_name($img, $profile);
    $dest = $this->conf->base . '/' . $this->conf->dest . '/' . $name;

    // $src = $img->download_file ?: $img->_id;
    $src = $img->download_file ?: $img->path;
    dbg("[image] resizer", $src, $dest, $profile, is_array($profile));
    $res = self::resize($src, $dest, $img, $profile, $this->conf->base);
    if ($profile->fourc) {
      $this->set_tags($dest, $profile);
    }
    dbg('[image] finished', $res);
    $img->resize_url = $name;
    $img->resize = $res;
    return $img;
    // return $opts['image_prefix'] . '/' . $name;
  }

  // <img> tag
  function image_tag(string|array|asset $img, profile|string $opts, array $tagopts = []): string {
    $resize = $this->run($img, $opts);
    if (!$resize) {
      return "";
    }

    if (self::is_remote($resize->resize_url)) {
      return $this->html_cdn($resize, $tagopts);
    } else {
      return $this->html($resize, $tagopts);
    }
  }

  function image_url(string|array|asset $img, profile|string $opts) {
    $resize = $this->run($img, $opts);
    if (!$resize) {
      return "";
    }

    return $this->prefixed_url($resize->resize_url);
  }

  function prefixed_url($url, $opts = []) {
    $url = PATH_PREFIX . $this->conf->path . '/' . $url;
    return $url;
  }

  /*
    set most important iptc/exif data
    Caption/description, Creator, Copyright Notice, Credit Line (= the 4Cs)
    https://www.iptc.org/std/photometadata/documentation/userguide/#_rights_information
    https://iptc.org/standards/photo-metadata/social-media-sites-photo-metadata-test-results-2019/
    https://de.wikipedia.org/wiki/IPTC-IIM-Standard
*/
  function set_tags(string $dest, profile $profile) {
    $tags = [
      'caption' => '2#120',
      'creator' => '2#80',
      'copyright' => '2#116',
      'credit' => '2#110',
    ];
    $caption = $profile->fourc['caption'] ?? $profile->fourc['caption'] ?? $profile->alt ?? "";
    $creator = $profile->fourc['creator'] ?? $profile->author ?? "";
    $copyright = $profile->fourc['copyright'] ?? "© All Rights Reserved.";
    $credit = $profile->fourc['credit'] ?? "© " . $creator;

    $utf8seq = chr(0x1b) . chr(0x25) . chr(0x47);
    $length = strlen($utf8seq);
    $data = chr(0x1C) . chr(1) . chr('090') . chr($length >> 8) . chr($length & 0xFF) . $utf8seq;
    foreach ($tags as $tname => $tcode) {
      if ($$tname) {
        $tag = substr($tcode, 2);
        $data .= $this->iptc_make_tag(2, $tag, $$tname);
      }
    }
    $content = iptcembed($data, $dest);
    if ($content !== false) {
      $fp = fopen($dest, "wb");
      fwrite($fp, $content);
      fclose($fp);
    }
  }


  public function iptc_make_tag($rec, $data, $value) {
    $length = strlen($value);
    $retval = chr(0x1C) . chr($rec) . chr($data);

    if ($length < 0x8000) {
      $retval .= chr($length >> 8) .  chr($length & 0xFF);
    } else {
      $retval .= chr(0x80) .
        chr(0x04) .
        chr(($length >> 24) & 0xFF) .
        chr(($length >> 16) & 0xFF) .
        chr(($length >> 8) & 0xFF) .
        chr($length & 0xFF);
    }

    return $retval . $value;
  }

  static public function get_resizer($base): void {
    // The internal adapter
    $adapter = new InMemoryFilesystemAdapter();

    // The FilesystemOperator
    $cache = new Filesystem($adapter);
    dbg("+++ glide src path", $base);
    $server = \League\Glide\ServerFactory::create([
      'source' => $base,
      'cache' => $cache,
      //'source_path_prefix' => '/'
    ]);
    self::$resizer = $server;
    self::$cache = $cache;
  }

  function asset_from_file($path): ?asset {
    //var_dump($gopts);
    $fname = $this->conf->base . '/' . $this->conf->src . '/' . $path;
    dbg("++ get input info", $fname);
    $info = getimagesize($fname);
    if (!$info) {
      return null;
    }
    return new asset(
      $path,
      $this->conf->src,
      $fname,
      $fname,
      $info[0],
      $info[1],
      $info['mime']
    );
  }

  function html(asset $res, array $tagopts) {
    //print_r($opts);
    if ($tagopts["noheight"] ?? null) {
      $height = "";
    } else {
      $height = sprintf('height="%s"', $res->resize[1]);
    }
    return sprintf(
      '<img src="%s" width="%s" %s alt="%s" loading="lazy" class="%s">',
      $this->prefixed_url($res->resize_url),
      $res->resize[0],
      $height,
      \htmlspecialchars($tagopts['alt'] ?? ''),
      \htmlspecialchars($tagopts['class'] ?? '')
    );
  }

  function html_cdn(asset $res, $tagopts) {
    return sprintf(
      '<img src="%s" alt="%s" class="%s">',
      $res->resize_url,
      \htmlspecialchars($tagopts['alt'] ?? ''),
      \htmlspecialchars($tagopts['class'] ?? '')
    );
  }
  static function resize(string $src, string $dest, asset $img, profile $profile, string $base): array {
    dbg('+++++ resize', $src, $dest, $profile);
    // copy-only
    if (!$profile->size) {
      dbg("[resizer] img copy only");
      `cp $src $dest`;
      return getimagesize($dest);
    }

    if (file_exists($dest)) {
      dbg("[resizer] img exists");
      return getimagesize($dest);
    }

    if (!\file_exists($src)) {
      dbg("+++ src doesn't exists", $src);
      return [];
    }

    // TODO: this should not be needed
    $src = \str_replace($base, '', $src);
    $fp = $img->fp;
    if (!$fp) $fp = $profile->fp;
    dbg("++ focal point", $fp);
    if (!$profile->w || !$profile->h) {
      $new = self::resize_one_side($src, $dest, $profile->w, $profile->h);
      //var_dump($new);
    } else {
      $mode = $profile->mode;
      if (!$mode) {
        $mode = 'fit';
      }
      if ($mode == 'fill' && $fp) {
        $new = self::resize_fp($src, $dest, $profile->w, $profile->h, $fp);
      } else {
        $new = self::resize_two_sides($src, $dest, $profile->w, $profile->h, $mode);
      }
    }
    //var_dump($server);
    if ($new) {
      \file_put_contents($dest, self::$cache->read($new));
      return \getimagesize($dest);
    }
    return [];
  }

  static function resize_one_side($src, $dest, $w, $h) {
    $p = $w ? ['w' => $w] : ['h' => $h];
    #dbg('+++ server', $src, $p);
    $p['q'] = 72;
    $p['sharp'] = 5;
    return self::$resizer->makeImage($src, $p);
  }

  static function resize_two_sides($src, $dest, $w, $h, $mode) {
    $mode = $mode == 'fit' ? 'contain' : 'crop';
    $p = ['w' => $w, 'h' => $h, 'fit' => $mode];
    $p['q'] = 72;
    $p['sharp'] = 5;
    #dbg('+++ server', $src, $p);
    return self::$resizer->makeImage($src, $p);
  }
  /*
    crop with focal point
*/
  static function resize_fp($src, $dest, $w, $h, $fp) {

    $p = [
      'w' => $w,
      'h' => $h,
      'fit' => 'crop-' . round($fp[0] * 100) . '-' . round($fp[1] * 100)
      //'fit' => 'crop-50-20'
    ];
    $p['q'] = 72;
    $p['sharp'] = 5;
    dbg('+++ server resize_fp', $src, $p);
    return self::$resizer->makeImage($src, $p);
  }

  public function fetch_profile($name) {
    // copy only
    if ($name == "") {
      return new profile;
    }
    if (!isset($this->conf->profiles[$name])) {
      // ad hoc 600x 600x300 etc
      if (preg_match("/^[x\d]{2,}$/", $name)) {
        return new profile(size: $name);
      } else {
        throw new Exception("imageprofile $name not found");
      }
    } else {
      return $this->conf->profiles[$name];
    }
  }

  public function get_profile(profile|string $opts): profile {
    if (is_string($opts)) {
      return $this->fetch_profile($opts);
    }
    if ($opts->name) {
      return $this->fetch_profile($opts->name)->merge($opts);
    }
    return $opts;
  }

  function get_name(asset $img, profile $resize_profile) {
    $significant = 'size mode fp fourc';
    $url = $img->url;
    $profile = (array) $resize_profile;
    if ($img->fp) $profile["fp"] = $img->fp;

    $profile = array_intersect_key($profile, array_flip(explode(' ', $significant)));
    ksort($profile);
    $info = \pathinfo($url);
    $hash = \md5($info['filename'] . '?' . http_build_query($profile));
    if (!$profile['size']) {
      $profile['size'] = 'ypoc';
    }
    return $info['filename'] . '--' . $hash . '-' . $profile['size'] . '.' . $info['extension'];
  }

  static function download_remote_image($url, $file_name) {
    if (file_exists($file_name) || file_put_contents($file_name, file_get_contents($url))) {
      $info = getimagesize($file_name);
      if (!in_array($info['mime'], ['image/jpeg', 'image/png'])) {
        unlink($file_name);
        return [false, "Unsupported file type"];
      } else {
        return [true, $info];
      }
    }
    return [false, "Download failed"];
  }

  // TODO: make it more bulletproof
  static public function is_remote($url) {
    return (preg_match("!^https?://!", $url));
  }
}
/*

1 Calculate the final image aspect ratio:
k=Wr/Hr,
where Wr and Hr - the width and height of the image of the future
2 Determine the maximum rectangle that will fit into the original image:
if Wr >= Hr
then Wm = Wi, Hm = Wi/k
else Hm = Hi, Wm = Hm*k,
where Wi, Hi - the size of the original, and Wm, Hm - the maximum size of the rectangle.
3 We calculate new coordinates for the focal point:
fx2 = fx*Wm/Wi,
fy2 = fy*Hm/Hi,
fx, fx - the coordinates of the focus on the original image
4 We do the actual cropping by shifting the rectangle by the difference between the old and new coordinates of the focal point:
crop(Wm, Hm, (fx-fx2), (fy-fy2))
5 Reduce the result to the desired size:
resize(Wr, Hr)
*/
