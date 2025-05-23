<nav>
    <details :foreach="section in chapters.index"
        :open="section.sid==current_section?'open':''"
        :class="{active: sid==current_section}">
        <summary>{{ section.title }}</summary>
        <a :foreach="chapter in section.c" :href="helper.path(chapter)"
            :class="{active: chapter._file.path == current.path}">{{chapter.title}}</a>
    </details>
</nav>

<?php

$chapters = $helper->get('chapter_index');
// var_dump($chapters);die();
$current_section = $props->current->dir ? basename($props->current->dir) : basename($chapters->index[0]->_file->dir);
