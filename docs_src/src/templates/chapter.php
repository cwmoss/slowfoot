<?php

use slowfoot_plugin\markdown\markdown_plugin;

layout("default");
?>
<article>
    <h1><?= $page['title'] ?></h1>
    <?= $markdown($page['mdbody']) ?>
</article>

<aside>
    <h4>ON THIS PAGE</h4>
    <?= markdown_plugin::markdown_toc($page['mdbody']) ?>
</aside>