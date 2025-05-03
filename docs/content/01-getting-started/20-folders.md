---
title: Directory structure
---

A basic project directory looks like this:

```
my-website
├── content
│  ├── 01-getting-started
│  │  ├── 01-getting-started.md
│  │  └── 02-folders.md
...
├── composer.json
├── site.php
├── slowfoot-config.php
├── src
│  ├── assets
│  │  ├── css
│  │  │  ├── accordion.scss
│  │  │  ├── app.css
│  │  │  ├── app.css.scss
│  │  │  └── prism.css
│  │  ├── gfx
│  │  │  └── favicon-96x96.png
│  │  └── js
│  │     ├── app.js
│  │     └── prism.js
│  ├── layouts
│  │  └── default.php
│  ├── pages
│  │  └── index.php
│  ├── partials
│  │  └── nav.php
│  └── templates
│     └── chapter.php
└── var
   ├── download
   ├── rendered-images
   │  └── starship--9b668a51ad73789cd9f94f1417e9ab4f-ypoc.jpg
   ├── slowfoot.db
   └── template

```

## slowfoot-config.php

[Configuration file](/05-ref/01-config) for the project.

## composer.json

Lists all the composer dependencies of your project.

## site.php (optional)

For custom code, like loader functions, template helpers etc. this is just a recommodation. use what ever naming/loading mechanism that needs to be.

## src/

This is where your site sources live. Templates, Layouts, Stylesheets, Javascripts, Graphics.

## var/

This directory is managed by slowfoot. Here lives the Database, downloaded and processed images.
