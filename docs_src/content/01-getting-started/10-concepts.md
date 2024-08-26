---
title: Core Concepts
---

# Why

slowfoot is a static site generator. it should be a perfect fit for headless CMSs. It borrows concepts from gridsome, sanity io, eleventy... I just didn't want to die in js/node/npm hell. so php ftw :)

## Your Content is data

slowfoot sees your content as a dataset of documents. these documents (or records) are loaded via data loaders, transformed and run through templates to produce the html output of your website.

every document needs at least a global ID `_id` and a type `_type` property. other than that it makes no assumtions about your content. design the structure of your content as you need it. slowfoot will be a great compagnion to your headless CMS.

in your templates, you can query your content.

in your routing roules you can define which templates to use for certain data types (collections) and under which path they will be published. these template get prepopulated with the records.

## Content Sources

Name, loader function

## Content Projection

Routing, template rules, page templates

## Templates

ATM simple php template engine. Produces HTML out of your content.

## Assets

I'm making a distinctions between assets and images for now. Assets is everything static, like css, javascript, graphics for your website. ATM there is no special build pipeline. Everything gets copied over to the build directory.

## Images

Images are assets, that are part of your contents. They will go through the image processing pipeline.
