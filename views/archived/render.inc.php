<?php

    // Support for CLI
    $host = 'unknown';
    if (isset($_SERVER['HTTP_HOST']) === true) {
        $host = $_SERVER['HTTP_HOST'];
    }

    // error headers
    $protocol = 'HTTP/1.1';
    if (isset($_SERVER['SERVER_PROTOCOL']) === true) {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
    }
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
    $content = file_get_contents('assets/bootstrap-combined.min.css', true);
    echo $content;
?>
        </style>
        <style type="text/css">
            .container {
                padding: 40px 0 0;
            }
            h1 {
                font-size: 24.5px;
                border-bottom: 1px solid #eee;
                font-weight: normal;
                padding-bottom: 10px;
                margin-bottom: 10px;
            }
            h2 {
                font-weight: normal;
                line-height: 20px;
                border: solid #eee;
                border-width: 0 0 1px;
                padding: 10px 0;
                margin: 20px 0 10px;
                font-size: 20px;
            }
            h3 {
                margin-top: 0;
                padding: 0;
                font-size: 15px;
                font-weight: normal;
                line-height: 20px;
            }

            .block {
                position: relative;
                padding-top: 10px;
            }
            .block .snip {
                position: relative;
            }
            .block .snip .badges {
                width: 400px;
                position: absolute;
                left: -408px;
                top: 6px;
            }
            .block .snip .badges .badge.badge-inverse {
                float: right;
                margin-bottom: 2px;
            }
            .block .snip .badges .badge.badge-info {
                float: right;
                clear: right;
            }
            .block .snip pre {
                overflow: auto;
                word-wrap: normal;
                white-space: pre;
                margin-bottom: 10px !important;
            }
            .block .snip pre.prettyprint.gutter {
                font-size: 11px;
                padding-left: 20px;
                -webkit-box-shadow: inset 50px 0 0 #fbfbfc, inset 51px 0 0 #ececf0;
                -moz-box-shadow: inset 50px 0 0 #fbfbfc, inset 51px 0 0 #ececf0;
                box-shadow: inset 50px 0 0 #fbfbfc, inset 51px 0 0 #ececf0;
            }
            .block .snip pre li.focus {
                color: #468847 !important;
            }
            .block .snip pre li.focus span {
                color: inherit;
            }
            .block.first .snip pre li.focus {
                color: #B94A48 !important;
            }
            .block .hovered {
                position: absolute;
                width: 12px;
                height: 12px;
                background-color: #468847;
                border-radius: 4px;
                margin: 4px 0 0;
            }
            .block.first .hovered {
                background-color: #B94A48;
            }
            footer {
                text-align: right;
                font-size: 13px;
                border-top: 1px solid #eee;
                margin-top: 15px;
                padding-top: 20px;
                padding-bottom: 40px;
                color: #777;
            }
            footer span {
                padding: 0 0 0 24px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="span8 offset2">
                    <h1><?= ($errorMessage) ?></h1>
<?php
    foreach ($blocks as $x => $block):
        $classes = array('block');
        if ($x === 0) {
            array_push($classes, 'first');
        }
        if ($x === 1):
?>
                    <h2>Backtrace</h2>
<?php
        endif;
?>
                    <div class="<?= implode(' ', $classes) ?>" data-line-number="<?= ($block['line']) ?>">
                        <h3><?= ($block['path']) ?></h3>
                        <div class="snip">
                            <div class="badges">
                                <span class="badge badge-inverse">Line #<?= ($block['line']) ?></span>
                                <?php if ($block['functionName'] !== false): ?>
                                    <span class="badge badge-info"><?= ($block['functionName']) ?></span>
                                <?php endif; ?>
                            </div>
                            <pre class="prettyprint gutter linenums:<?= ($block['start']) ?>"><?= ($block['output']) ?></pre>
                        </div>
                    </div>
<?php
    endforeach;
?>
                </div>
            </div>
            <div class="row">
                <footer class="span8 offset2">
                    <span><?= date('D, d M o G:i:s T') ?></span>
                    <span><?= (IP) ?></span>
                    <span><?= ($host) ?></span>
                </footer>
            </div>
        </div>
        <script type="text/javascript">
<?php
    $content = file_get_contents('assets/prettify.js', true);
    echo $content;
?>
<?php
    $content = file_get_contents('assets/jQuery.min.js', true);
    echo $content;
?>
        </script>
        <script type="text/javascript">
            prettyPrint();
            var blocks = $('.block');
            jQuery.each(blocks, function(index, block) {
                var focusingLineNumber = $(block).attr('data-line-number'),
                    first = $(block).find('li[value]'),
                    lines = first.nextAll(),
                    previous = first.val();

                first.attr('data-line', first.val());
                jQuery.each(lines, function(index, line) {
                    $(line).attr('data-line', previous + 1);
                    ++previous;
                });

                // selected line
                var selected = $(block).find('li[data-line="' + (focusingLineNumber) + '"]');
                selected.addClass('focus');

                // create a hovered-focus element for the errored-line
                var hovered = $('<div class="hovered"></div>'),
                    position = selected.position();
                hovered.css({
                    left: position.left - 75,
                    top: position.top + 40
                });
                $(block).append(hovered);
            });
        </script>
    </body>
</html>
