---
title: Core Concepts
---

# Your Content is data

slowfoot sees your content as a dataset of records. these records are loaded via data loaders, transformed and run through templates to produce the html output of your website.

every records needs at least a global ID `_id` and a type `_type`.

in your templates, you can query your content.

in your routing roules you can define which templates to use for certain data types (collections) and under which path they will be published. these template get prepopulated with the records.
