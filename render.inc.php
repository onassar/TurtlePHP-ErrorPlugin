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
        <!-- <link rel="stylesheet" type="text/css" href="http://twitter.github.io/bootstrap/assets/css/bootstrap.css" /> -->
        <!-- <link rel="stylesheet" type="text/css" href="http://twitter.github.io/bootstrap/dist/css/bootstrap.css" /> -->
        <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" />
        <!-- <link rel="stylesheet" type="text/css" href="http://twitter.github.io/bootstrap/assets/js/google-code-prettify/prettify.css" /> -->
        <link rel="stylesheet" type="text/css" href="//raw.githubusercontent.com/google/code-prettify/master/src/prettify.css" />
        <script type="text/javascript">
        //<![CDATA[
            var start=(new Date).getTime(),booted=[],included=false,required=[],js=function(e,t){if(arguments.length===0){t=function(){};e=[]}else if(arguments.length===1){t=e;e=[]}var n=function(e,t){var n=document.createElement("script"),r=document.getElementsByTagName("script"),s=r.length,o=function(){try{t&&t()}catch(e){i(e)}};n.setAttribute("type","text/javascript");n.setAttribute("charset","utf-8");if(n.readyState){n.onreadystatechange=function(){if(n.readyState==="loaded"||n.readyState==="complete"){n.onreadystatechange=null;o()}}}else{n.onload=o}n.setAttribute("src",e);document.body.insertBefore(n,r[s-1].nextSibling)},r=function(e,t){for(var n=0,r=e.length;n<r;++n){if(e[n]===t){return true}}return false},i=function(e){log("Caught Exception:");log(e.stack);log("")};if(included===false){if(typeof e==="string"){e=[e]}e=e.concat(required);included=true}if(typeof e==="string"){if(r(booted,e)){t()}else{booted.push(e);n(e,t)}}else if(e.constructor===Array){if(e.length!==0){js(e.shift(),function(){js(e,t)})}else{try{t&&t()}catch(s){i(s)}}}},log=function(){if(typeof console!=="undefined"&&console&&console.log){var e=arguments.length>1?arguments:arguments[0];console.log(e)}},queue=function(){var e=[];return{push:function(t){e.push(t)},process:function(){var t;while(t=e.shift()){t()}}}}(),ready=function(e){var t=false,n=true,r=window.document,i=r.documentElement,s=r.addEventListener?"addEventListener":"attachEvent",o=r.addEventListener?"removeEventListener":"detachEvent",u=r.addEventListener?"":"on",a=function(n){if(n.type==="readystatechange"&&r.readyState!=="complete"){return}(n.type==="load"?window:r)[o](u+n.type,a,false);if(!t&&(t=true)){e()}},f=function(){try{i.doScroll("left")}catch(e){setTimeout(f,50);return}a("poll")};if(r.readyState==="complete"){e.call(window,"lazy")}else{if(r.createEventObject&&i.doScroll){try{n=!window.frameElement}catch(l){}if(n){f()}}r[s](u+"DOMContentLoaded",a,false);r[s](u+"readystatechange",a,false);window[s](u+"load",a,false)}},require=function(e){if(typeof e==="string"){e=[e]}required=required.concat(e)}
        //]]>
        </script>
        <!--[if lt IE 9]>
        <script src="/static/js/vendors/html5.js"></script>
        <![endif]-->
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
        <!-- <link rel="stylesheet" type="text/css" href="http://twitter.github.io/bootstrap/assets/css/bootstrap-responsive.css" /> -->
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
            <script type="text/javascript">
                queue.push(function() {
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
                });
            </script>
        </div> <!-- /container -->
        <script type="text/javascript">
        //<![CDATA[
            ready(function() {
                log('ready.post', (new Date()).getTime() - start);
                js(
                    [
                        // 'http://twitter.github.io/bootstrap/assets/js/google-code-prettify/prettify.js',
                        'https://raw.githubusercontent.com/google/code-prettify/master/src/prettify.js',
                        // 'https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js',
                        'https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js'
                    ],
                    function() {
                        log('js.post', (new Date()).getTime() - start);
                        queue.process();
                        log('queue.post', (new Date()).getTime() - start);
                    }
                );
            });
        //]]>
        </script>
    </body>
</html>
