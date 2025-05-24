<layout.default>
    <article>
        <h1>{{ page.title }}</h1>

        <sft.markdown :body="page.mdbody"></sft.markdown>

        <time :if="page.date">{{date}}</time>
    </article>
</layout.default>

<?php

use DateTime;

if ($props->page->date) $date = new DateTime($props->page->date)->format("d.m");
