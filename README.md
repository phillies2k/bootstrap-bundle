BootstrapBundle
===============

Version: **0.9.0**


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


### Themeing

Start creating your custom theme class by extending P2\Bundle\BootstrapBundle\Themeing\Theme. It represents a default implementation of all themeing methods, meaning bringing you the default bootstrap style on top.

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
necessary files and will add the theme to your applications assets configuration.

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

