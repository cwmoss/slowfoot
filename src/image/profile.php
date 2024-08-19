<?php

namespace slowfoot\image;
/*
'profiles' => [
            'cover' => [
                'size' => // '850x430', 
                '480x600',
                'mode' => 'fill',
                'fp' => [0.5, 0.2],
                '4c' => ['creator' => 'Robbie Wilhelm']
            ],
            'gallery' => [
                'size' => '640x',
                '4c' => ['creator' => 'Robbie Wilhelm']
            ],
            'gallery_ls' => [
                'size' => '800x',
                '4c' => ['creator' => 'Robbie Wilhelm']
            ]
        ]
            */

class profile {
    public $w;
    public $h;

    public function __construct(
        public string $size = "",
        public ?string $mode = null,
        public ?array $fp = null,
        public ?array $fourc = null,
        public ?string $alt = null,
        public ?string $author = null,
        public ?string $name = null
    ) {
        $wh = explode('x', $size);
        $this->w = $wh[0] ?: null;
        $this->h = $wh[1] ?? null;
    }

    public function merge(self $profile) {
        $p = clone ($this);
        foreach ($profile as $k => $v) {
            if (!is_null($v)) {
                $p->$k = $v;
            }
        }
        return $p;
    }
}
