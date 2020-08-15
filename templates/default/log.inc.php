Message:            <?= $throwable->getMessage() . (PHP_EOL) ?>
File:               <?= $throwable->getFile() . (PHP_EOL) ?>
Line:               <?= $throwable->getLine() . (PHP_EOL) ?>
IP:                 <?= (IP) . (PHP_EOL) ?>
URI:                <?= ($_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? null) . (PHP_EOL) ?>
Referrer:           <?= ($_SERVER['HTTP_REFERER'] ?? ':unknown:') . (PHP_EOL) ?>
Request Method:     <?= ($_SERVER['REQUEST_METHOD'] ?? ':unknown:') . (PHP_EOL) ?>
