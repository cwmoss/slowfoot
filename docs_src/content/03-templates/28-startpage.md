---
title: Start Page
---

The Startpage is ether a page produced via `pages/index.php` or via a collection template, that produces the path `index`.

## Redirect

You could also have a very basic `pages/index.php` template, that does a client side redirect.

```html
<!DOCTYPE html>
<meta
  http-equiv="refresh"
  content="0; URL=<?= path_page('/01-getting-started/01-getting-started') ?>"
/>
<title>you will be redirect to our start page</title>
```
