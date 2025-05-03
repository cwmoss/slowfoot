<?php
// $image_tag = $helper->image_tag;
dbg("image", $props->src);
print $helper->image_tag($props->src, $props->size, ['alt' => $props->alt ?? ""]);
