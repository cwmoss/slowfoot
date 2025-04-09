<?php

namespace slowfoot;

interface template_contract {
    public function run(string $_template, array $data, array $helper, context $__context): string;
}
