
*Error*
Message:            <?= $throwable->getMessage() . (PHP_EOL) ?>
File:               <?= $throwable->getFile() . (PHP_EOL) ?>
Line:               <?= $throwable->getLine() . (PHP_EOL) ?>
IP:                 <?= (IP) . (PHP_EOL) ?>
Server Name:        <?= ($_SERVER['SERVER_NAME'] ?? null) . (PHP_EOL) ?>
Request URI:        <?= ($_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? null) . (PHP_EOL) ?>
HTTP Referrer:      <?= ($_SERVER['HTTP_REFERER'] ?? ':unknown:') . (PHP_EOL) ?>
Request Method:     <?= ($_SERVER['REQUEST_METHOD'] ?? ':unknown:') . (PHP_EOL) ?>
User Agent:         <?= ($_SERVER['HTTP_USER_AGENT'] ?? ':unknown:') . (PHP_EOL) ?>
