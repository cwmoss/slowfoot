<?php

namespace slowfoot;

// TODO: cases without templates
class no_template implements template_contract {
    public function run(string $_template, array $data, array $helper, context $__context): string {
        throw new \Exception('Not implemented');
    }
}
