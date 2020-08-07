TurtlePHP-ErrorPlugin
=====================

### Sample plugin loading:
``` php
require_once APP . '/plugins/TurtlePHP-BasePlugin/Base.class.php';
require_once APP . '/plugins/TurtlePHP-ErrorPlugin/Error.class.php';
$path = APP . '/config/plugins/error.inc.php';
Plugin\Error::setErrorPath($path);
Plugin\Error::init();
```

### Sample UI
![Sample Error](http://i.imgur.com/qaRZ6.png)
