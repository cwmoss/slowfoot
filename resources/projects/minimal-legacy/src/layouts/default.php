<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $_context->config->site_name ?></title>
  <link rel="stylesheet" href="<?= path_asset('/css/site.css', true) ?>" type="text/css">
  <script src="<?= path_asset('/js/site.js', true) ?>"></script>
</head>

<body>
  <?= $content ?>
</body>

</html>
