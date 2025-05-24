<nav>

    <a :foreach="navigation as path => title" :href="path"
        :class="{active: false}">{{title}}</a>

</nav>
<style>
    root {
        display: flex;
        gap: 1rem;
    }
</style>

<?php
$navigation = [
    "/" => "Home",
    "/about" => "About Me"
];


// var_dump($chapters);die();
// $current_section = $props->current->dir ? basename($props->current->dir) : basename($chapters->index[0]->_file->dir);
