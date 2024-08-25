# W.I.P slowfoot

php >= 8.2

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
