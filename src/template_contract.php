<?php

namespace slowfoot;

// TODO: preprocess, etc.

abstract class template_contract {
    public string $file_extension = ".php";
    abstract public function run(string $_template, array $data, array $helper, context $__context): string;
}
