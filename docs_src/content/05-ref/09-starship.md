---
title: Starship Configuration
---

Add this to your `starship.toml` to get some nice info prompt about your slowfoot project:

```toml
[custom.slowfoot]
command = 'vendor/bin/slowfoot starship'
detect_files = ['slowfoot-config.php']
```

![prompt with slowfoot info](./starship.jpg)

More on starship: [https://starship.rs/guide/]
