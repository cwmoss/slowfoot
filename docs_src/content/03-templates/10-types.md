---
chapter_title: Templates
title: Template Types
---

Templates are written in plain php. There are different types of templates: page templates (folder `pages`), collection templates (folder `templates`), layout templates (folder `layouts`) and partials templates (folder `partials`).

## Page Templates

Page Templates can include a special tag `<page-query>`. Here you can query contents that will be available under the `$page` variable.

```html pages/categories.php
<page-query limit="10"> *(_type="galley")order(created DESC) </page-query>

<?php layout('default'); ?>
<main>
  <?foreach($page as $gallery){?>
  <h2>
    <a href="<?= $path($gallery) ?>"><?= $gallery['title'] ?></a>
  </h2>
  <?}?>
</main>
```

## Collection Templates

Collection templates are named after their tyoe: If you have a type `category` the template would be `templates/category.php`. It will be executed for every document of this collection.

## Layout Templates

Layout templates are invoked with the `layout(string $name)` function in page or collection templates.

```html
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, user-scalable=yes"
    />
    <link
      rel="stylesheet"
      href="<?= path_asset('/css/site.css', true) ?>"
      type="text/css"
    />
    <link data-vue-tag="ssr" rel="icon" type="image/png" sizes="32x32" href="
    <?= path_asset("/gfx/favicon-32.png") ?>
    ">
    <title><?= $title ?></title>
  </head>

  <body class="<?= $body_classes ?>">
    <div id="app" class="layout">
      <?= $partial("header") ?>

      <main>
        <?= $content ?>
      </main>

      <?= $partial("footer") ?>
    </div>

    <script src="<?= path_asset('/js/app.js') ?>"></script>
  </body>
</html>
```

## Partials

Partials are called via `$partial(string $name, ?array $data)` helper function.
