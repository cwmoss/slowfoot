<?php

namespace slowfoot;

// TODO: preprocess, etc.

interface template_contract {
    public function run(string $_template, array $data, array $helper, context $__context): string;
}
