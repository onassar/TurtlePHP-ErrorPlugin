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
    $path = 'assets/vendors/animate.css/v4.1.0/animate.min.css';
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
                <h3>
                    <div class="copy"><?= ($block['path']) ?></div>
                    <?php
                        $copyValue = $block['path'];
                        $pieces = explode('TurtlePHP/', $copyValue);
                        $copyValue = array_pop($pieces);
                        $copyValue = 'TurtlePHP/' . ($copyValue);
                        $copyValue = ($copyValue) . ':' . ($block['line']);
                    ?>
                    <a href="#" class="icon copy" data-copy-value="<?= ($copyValue) ?>" title="Copy to clipboard">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABmJLR0QA/wD/AP+gvaeTAAAA7UlEQVRoge2ZQQ6CMBBFn8at8QK65uwSEtcuvJGaUA+gC1xo0wgttR90XsJmQsp/NHSSAQzDCLEDGsAB98JXlvAXQfBsAo0wfLTAIlBzwDp2oYyEMkXd7L+FqAVLs1QHGIsJqEkVKNknHHAAqqHh+o41VZ+4Pp89WkDZJ2o/TMoxquwTDti8FlIESveJj8/721NoMpiAGhNQYwJqTECNCagxATUmoMYE1MxeYBWoTXoW6jP7HTABNaFvoI8b74OtLL+FBtL6hZQdOGUIksoxxyIV3aC19Fz0DGxzCEA3Ja7ptvTbwVtgnzO8YfwSD6GzArx9pr2cAAAAAElFTkSuQmCC"/>
                    </a>
                </h3>
                <div class="content">
                    <div class="badges">
                        <div class="badge lineNumber">Line #<?= ($block['line']) ?></div>
                        <?php if ($block['functionName'] !== null): ?>
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
            <footer>
                <span><?= date('D, d M o G:i:s T') ?></span>
                <span><?= (IP) ?></span>
                <span><?= ($_SERVER['HTTP_HOST'] ?? ':unknown:') ?></span>
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
