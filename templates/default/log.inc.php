Message:   <?= $throwable->getMessage() . (PHP_EOL) ?>
File:      <?= $throwable->getFile() . (PHP_EOL) ?>
Line:      <?= $throwable->getLine() . (PHP_EOL) ?>
IP:        <?= (IP) . (PHP_EOL) ?>
URI:       <?= ($_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? null) . (PHP_EOL) ?>
