<?php

    // Headers
    $host = $_SERVER['HTTP_HOST'] ?? ':unknown:';
    $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    header(($protocol) . ' 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 7200');
    header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Error</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <style type="text/css">
<?php
    $path = 'assets/vendors/normalize.css/v8.0.0/normalize.css';
    $content = file_get_contents($path, true);
    echo $content;
?>
        </style>
        <style type="text/css">
<?php
    $path = 'assets/vendors/code-prettify/e006587b4a893f0281e9dc9a53001c7ed584d4e7/loader/skins/' . ($skin) . '.css';
    $content = file_get_contents($path, true);
    echo $content;
?>
        </style>
        <style type="text/css">
<?php
    $path = 'assets/default.css';
    $content = file_get_contents($path, true);
    echo $content;
?>
        </style>
        <script type="text/javascript">
<?php
    $path = 'assets/vendors/code-prettify/e006587b4a893f0281e9dc9a53001c7ed584d4e7/loader/prettify.js';
    $content = file_get_contents($path, true);
    echo $content;
?>
        </script>
    </head>
    <body class="skin-<?= ($skin) ?>">
        <div class="container">
            <h1><?= ($errorMessage) ?></h1>
<?php
    foreach ($blocks as $index => $block):
        if ($index === 1):
?>
            <h2>Backtrace</h2>
<?php
        endif;
?>
            <div class="block" data-line-number="<?= ($block['line']) ?>" data-highlight-index="<?= ($block['line'] - $block['start']) ?>">
                <h3><?= ($block['path']) ?></h3>
                <div class="content">
                    <div class="badges">
                        <div class="badge lineNumber">Line #<?= ($block['line']) ?></div>
                        <?php if ($block['functionName'] !== false): ?>
                            <div class="badge functionName"><?= ($block['functionName']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="flag"></div>
                    <pre class="prettyprint linenums:<?= ($block['start']) ?>"><?= ($block['output']) ?></pre>
                </div>
            </div>
<?php
    endforeach;
?>
            <footer class="span8 offset2">
                <span><?= date('D, d M o G:i:s T') ?></span>
                <span><?= (IP) ?></span>
                <span><?= ($host) ?></span>
            </footer>
        </div>
        <script type="text/javascript">
<?php
    $path = 'assets/default.js';
    $content = file_get_contents($path, true);
    echo $content;
?>
        </script>
    </body>
</html>
