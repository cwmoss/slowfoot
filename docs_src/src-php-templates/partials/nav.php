<?php
//$chapters = $query('chapter() order(_file.path)');
debug_js("current", $current);
$chapters = $get('chapter_index');

$current_section = $current->dir ? basename($current->dir) : basename($chapters->index[0]->_file->dir);

debug_js('chapters', $chapters);

// var_dump($current);
?>
<nav>

    <?php foreach ($chapters->index as $section) {
        $open = $section->sid == $current_section ? 'open' : '';
        $active = $section->sid == $current_section ? 'active' : ''; ?>

        <details <?= $open ?> class="<?= $active ?>">
            <summary><?= $section->title ?></summary>
            <?php foreach ($section->c as $chapter) {
                $active = $chapter->_file->path == $current->path ? 'active' : ''; ?>
                <a href="<?= $path($chapter) ?>" class="<?= $active ?>"><?= $chapter->title ?></a>
            <?php
            } ?>
        </details>
    <?php
    } ?>


</nav>