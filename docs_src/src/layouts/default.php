<?php
$title = $__context->config->site_name;

?>
<!doctype html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <link rel="SHORTCUT ICON" type="image/png" href="<?= path_asset('/gfx/favicon-96x96.png') ?>">

    <link rel="stylesheet" href="<?= path_asset('/css/prism.css', true) ?>" type="text/css">
    <link rel="stylesheet" href="<?= path_asset('/css/app.css', true) ?>" type="text/css">

    <script src="<?= path_asset('/js/app.js') ?>"></script>
    <title><?= $title ?></title>

</head>

<body>

    <header>
        <div class="logo">slowfoot Docs <a href="https://github.com/cwmoss/slowfoot">github</a></div>
    </header>



    <main>

        <?= $partial('nav', ['current_id' => $page['_id'], 'current' => $page['_file'] ?? []]) ?>

        <?= $content ?>

    </main>

    <footer>
        <div class="content">
            &copy; 2024
        </div>
    </footer>
    <script src="<?= path_asset('/js/prism.js') ?>"></script>
</body>

</html>