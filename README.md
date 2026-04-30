[![Make Doku](https://github.com/cwmoss/slowfoot/actions/workflows/doku.yml/badge.svg)](https://github.com/cwmoss/slowfoot/actions/workflows/doku.yml)

# slowfoot W.I.P

requires php >= 8.5

your content is data. slowfoot transforms it into beautiful websites.

## how it works

### fetching

you define your content sources. slowfoot fetches every source and creates a dataset of documents. the basic schema for documents is:

- string `_id` a global id of the document
- string `_type` a document type

### projection

you define your web page paths per type. for every type, if there is a template, all documents are projected to web pages though the template.
a document can have multiple paths/templates defined. example: you have a type `artist` and 2 pages: `{artist}/` with the bio and `{artist}/works/` with a list of works.

### templates

with the templates, you define the output. default template engine is `phuety`. in your templates you can query all data that was previously fetched. slowfoot supports the generation of images and handling of css and javascript.

## config

everything will change!

https://cwmoss.github.io/slowfoot/

### sources

content sources have a unique name, a source type and opts

included source loader

- dataset, json nd, load_dataset()
- json, load_json()
- directory, markdown/ frontmatter, load_directory()

### types

types are content types with template, path pattern or path function

### hooks

available hooks

- on_load(row) => row || null

## pipeline

    include src/helper.php => SLOWFOOT_BASE (project root directory)
    read config.php => sources, types, hooks
    | load_sources
    | load template helper
    => dataset, paths
      | build pages from all types with defined templates
      | build pages from src/pages folder
      => website

## asset references

~/path/to.jpg relative to file-content-source-base
../parent/path/to.jpg relative to file-content-source-current-directory
./path/to.jpg relative to file-content-source-current-directory
/path/to.jpg relative to project-source-directory

## global cli

alias slowfoot="/Users/rw/dev/slowfoot/bin/slowfoot -d ."

## docker

docker run --rm -it -v ${PWD}:/project sft info

alias slowfoot="docker run --rm -it -p 1199:1199 -v ${PWD}:/project ghcr.io/cwmoss/slowfoot"

### control if page gets written

option A

return null or empty array, while fetching the document. no data, no output. good use case are drafts
for drafts you can also return a row with key `_draft`. the data will be only visible in development mode.
it will not be added to the production dataset.

also if document contains key `_no_path`, no path will be created and no page will be created

option B

use a path function in config that returns null for a document. effective, but a litte bit more involved.

option C

template return empty string. easy for designer but maybe not the most efficient

#### todo

resolve urls for pages, remove path_page function

remaining constants:

    SLOWFOOT_START
    SLOWFOOT_PREVIEW
    SLOWFOOT_NO_DEBUG
    ? SLOWFOOT_WEBDEPLOY

env:

    SLFT_BUILD_KEY
    SLFT_PROJECT_DIR
    SLFT_WRITE_PATH
    SLFT_PATH_PREFIX
    SLFT_PHP_BIN
    SLFT_DEPLOYER_LBR
    SLFT_WEBDEPLOY_ALLOWED_HOSTS

test requirements

    collator_create Intl
    iconv

TODO remove dependencies:

- front-yaml OK
- League CommonMark OK
- dotenv OK
- easydb OK
- ausi/slug-generator or jbroadway/urlify
- composer-runtime-api ?
- league/glide ?
- wrun => httpful ??
