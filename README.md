BootstrapBundle
===============

Version: **1.1.0**

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
* **"css/bootstrap.css"**
  The default twitter bootstrap css
* **"js/bootstrap.js"**
  The combined twitter bootstrap javascript library
* **"js/jquery.js"**
  The jQuery library


### Configuration

Default configuration:
```yaml
p2_bootstrap:
    use_themes: true
    use_forms: true
    use_extensions: true
    public_path: '%kernel.root_dir%/../web/themes'
    jquery_path: '%kernel.root_dir%/../components/jquery/jquery.js'
    source_path: '%kernel.root_dir%/../vendor/twitter/bootstrap'
    themes_path: '%kernel.root_dir%/Resources/themes'
    bootstrap_css: 'css/bootstrap.css'
    bootstrap_js: 'js/bootstrap.js'
    jquery_js: 'js/jquery.js'
    forms:
        defaults:
            horizontal: true
            inline: false
            prepend: true
            append: false
            help: ~
            info: ~
            icon: ~
            grid: [ 4, 8 ]
        allowed_types:
            horizontal: 'bool'
            inline: 'bool'
            prepend: 'bool'
            append: 'bool'
            help: [ 'null', 'string' ]
            info: [ 'null', 'string' ]
            icon: [ 'null', 'string' ]
            grid: 'array'
        allowed_values: []
```

### Console Commands

```bash
    app/console bootstrap:generate:themes
```
This will generate the themes for your application.

```bash
    app/console bootstrap:symlink:fonts
```
This command will symlink the bootstrap glyphicon fonts.


### Themeing

Start creating your custom theme class by extending [Theme](Themeing/Theme.php). It represents a default implementation of the [ThemeInterface](Themeing/ThemeInterface.php), meaning bringing you the default bootstrap style on top.

Every theme must implement the getName() method, that should return the unique name for this theme as it will be used for creating directories and generating file assets.

You can overwrite any method to return a custom value for your theme. Have a look at the example below.

```php
<?php

namespace Acme\Bundle\CustomBundle\Themeing;

use P2\Bundle\BootstrapBundle\Themeing\Theme\Theme;

/**
 * Class DarkTheme
 */
class DarkTheme extends Theme
{
    /**
     * {@inheritDoc}
     */
    public function getBodyBackground()
    {
        return '#111';
    }

    /**
     * {@inheritDoc}
     */
    public function getTextColor()
    {
        return '#fff';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'dark';
    }
}

```
Setup a service definition for your theme and tag it with 'bootstrap.theme'. The bundle will automatically generate all
necessary files and registers the themes for the theme builder.

```yaml

parameters:
    acme.themeing.dark_theme.class: Acme\Bundle\CustomBundle\Themeing\DarkTheme

services:

    # dark theme
    acme.themeing.dark_theme:
        class: %acme.themeing.dark_theme.class%
        tags:
            - { name: 'bootstrap.theme' }

```

Use your theme:

```twig
{% stylesheets filter="less" "@dark_style" %}
    <link rel="stylesheet" type="text/css" href="{{ asset_url }}">
{% endstylesheets %}
```

