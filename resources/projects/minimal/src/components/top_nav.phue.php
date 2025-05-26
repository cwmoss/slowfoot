<nav>

    <a :foreach="navigation as nav" :href="nav.path"
        :class="{active: nav.path==current}">{{nav.title}}</a>

</nav>
<style>
    root {
        display: flex;
        gap: 1rem;
    }

    a {
        color: pink;
        background-color: black;
        font-weight: bold;
        padding: 1rem;
        text-decoration: none;
    }

    .active {
        outline: 5px solid gold;
    }
</style>

<?php
$navigation = [
    ["path" => "/", "title" => "Home"],
    ["path" => $helper->path("about"), "title" => "About Me"]
];

$current = $props->globals->path;
if ($current == "/index") $current = "/";

// var_dump($chapters);die();
// $current_section = $props->current->dir ? basename($props->current->dir) : basename($chapters->index[0]->_file->dir);
