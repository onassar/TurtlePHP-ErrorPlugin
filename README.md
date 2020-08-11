TurtlePHP-ErrorPlugin
=====================

### Sample plugin loading:
``` php
require_once APP . '/plugins/TurtlePHP-BasePlugin/Base.class.php';
require_once APP . '/plugins/TurtlePHP-ErrorPlugin/Error.class.php';
$path = APP . '/config/plugins/error.inc.php';
TurtlePHP\Plugin\Error::setErrorPath($path);
TurtlePHP\Plugin\Error::init();
```

### Sample UI
![Sample Error](https://i.imgur.com/yIVT0ih.png)
