BootstrapBundle
===============

Version: **2.0.0-BETA**

[![Build Status](https://travis-ci.org/phillies2k/bootstrap-bundle.png?branch=master)](https://travis-ci.org/phillies2k/bootstrap-bundle)

### Installation

```javascript
    "require": {
        "p2/bootstrap-bundle": "dev-master"
    }
```

### Usage

Simply enable the bundle within your AppKernel.php file.

The Bundle will automatically create and provide the following assets for you:
* **@bootstrap_css "css/bootstrap.css"**
  The default twitter bootstrap css
* **@bootstrap_js "js/bootstrap.js"**
  The combined twitter bootstrap javascript library


### Configuration

Default configuration:
```yaml
p2_bootstrap:
    use_forms: true
    source_path: '%kernel.root_dir%/../vendor/twbs/bootstrap'
    bootstrap_css: 'css/bootstrap.css'
    bootstrap_js: 'js/bootstrap.js'
    forms:
        defaults:
            horizontal: true
            inline: false
            prepend: true
            append: false
            help: ~
            help_type: ~
            info: ~
            info_type: ~
            icon: ~
            grid: [ 4, 8 ]
        allowed_types:
            horizontal: 'bool'
            inline: 'bool'
            prepend: 'bool'
            append: 'bool'
            help: [ 'null', 'string' ]
            help_type: [ 'null', 'string' ]
            info: [ 'null', 'string' ]
            info_type: [ 'null', 'string' ]
            icon: [ 'null', 'string' ]
            grid: 'array'
        allowed_values: []
```
