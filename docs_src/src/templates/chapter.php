<?php

use slowfoot\plugins\markdown;

layout("default");
?>
<article>
    <h1><?= $page['title'] ?></h1>
    <?= $markdown($page['mdbody']) ?>
</article>

<aside>
    <h4>ON THIS PAGE</h4>
    <?= markdown::markdown_toc($page['mdbody']) ?>
</aside>