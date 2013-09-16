BootstrapBundle
===============

**This bundle is currently in development**



### Installation

    "require": {
        "p2/bootstrap-bundle": "dev-master"
    }

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

```
<?php

namespace Acme\Bundle\CustomBundle\Themeing;

use P2\Bundle\BootstrapBundle\Themeing\Theme;

class DarkTheme extends Theme
{
    public function getBodyBackground()
    {
        return '#111';
    }

    public function getTextColor()
    {
        return '#fff';
    }
}

```