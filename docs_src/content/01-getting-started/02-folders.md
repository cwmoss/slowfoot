---
title: Directory structure
---

A basic project looks like this:

```
.
├── composer.json
├── config.php
├── cache/
├── rendered-images/
├── static/
└── src/
    ├── index.php
    ├── helper.php
    ├── css/
    ├── gfx/
    ├── js/
    ├── fonts/
    ├── layouts/
    │   └── default.php
    ├── pages/
    │   ├── index.php
    │   └── search.php
    ├── templates/
    |    └── post.php
    └── partials/
        └── pager.php

```

## composer.json

Lists all the composer dependencies of your project.

## config.php

[Configuration file](/05-ref/01-config) for the project.
