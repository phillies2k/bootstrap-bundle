BootstrapBundle
===============

Version: **1.2.2**


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
* **"js/holder.js"**
  The holder.js library
* **"js/jquery.js"**
  The jQuery library


### Configuration

Default configuration:
```yaml
p2_bootstrap:
    use_themes: true                                                    # enables bootstrap themeing
    jquery_path: '%kernel.root_dir%/../components/jquery/jquery.js'     # path to the jquery source directory
    source_path: '%kernel.root_dir%/../vendor/twitter/bootstrap'        # path to the bootstrap source directory
    themes_path: '%kernel.root_dir%/Resources/themes'                   # path to store the themes to
    bootstrap_css: 'css/bootstrap.css'                                  # public bootstrap css path
    bootstrap_js: 'js/bootstrap.js'                                     # public bootstrap js library path
    jquery_js: 'js/jquery.js'                                           # public jquery path
    holder_js: 'js/holder.js'                                           # public holder.js path
```

### Themeing

Start creating your custom theme class by extending [Theme](Themeing/Theme.php). It represents a default implementation of the [ThemeInterface](Themeing/ThemeInterface.php), meaning bringing you the default bootstrap style on top.

Every theme must implement the getName() method, that should return the unique name for this theme as it will be used for creating directories and generating file assets.

You can overwrite any method to return a custom value for your theme. Have a look at the example below.

```php
<?php

namespace Acme\Bundle\CustomBundle\Themeing;

use P2\Bundle\BootstrapBundle\Themeing\Theme;

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
{% stylesheets filter="less"
  "../app/Resources/themes/dark/less/layout.less"
%}
    <link rel="stylesheet" type="text/css" href="{{ asset_url }}">
{% endstylesheets %}
```

