<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Error</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css" />
        <link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/js/google-code-prettify/prettify.css" />
        <script type="text/javascript">
        //<![CDATA[
            var start=(new Date()).getTime(),booted=[],included=false,required=[],js=function(assets,callback){if(arguments.length===1){callback=assets;assets=[]}var __boot=function(src,callback){var script=document.createElement("script"),scripts=document.getElementsByTagName("script"),length=scripts.length,loaded=function(){try{callback&&callback()}catch(exception){log("[Caught Exception]",exception)}};script.setAttribute("type","text/javascript");script.setAttribute("charset","utf-8");if(script.readyState){script.onreadystatechange=function(){if(script.readyState==="loaded"||script.readyState==="complete"){script.onreadystatechange=null;loaded()}}}else{script.onload=loaded}script.setAttribute("src",src);document.body.insertBefore(script,scripts[(length-1)].nextSibling)},__contains=function(arr,query){for(var x=0,l=arr.length;x<l;++x){if(arr[x]===query){return true}}return false};if(included===false){if(typeof assets==="string"){assets=[assets]}assets=assets.concat(required);included=true}if(typeof assets==="string"){if(__contains(booted,assets)){callback()}else{booted.push(assets);__boot(assets,callback)}}else{if(assets.constructor===Array){if(assets.length!==0){js(assets.shift(),function(){js(assets,callback)})}else{try{callback&&callback()}catch(exception){log("[Caught Exception]",exception)}}}}},log=function(){if(typeof(console)!=="undefined"&&console&&console.log){var args=arguments.length>1?arguments:arguments[0];console.log(args)}},queue=(function(){var stack=[];return{push:function(task){stack.push(task)},process:function(){var task;while(task=stack.shift()){task()}}}})(),ready=function(callback){var done=false,top=true,doc=window.document,root=doc.documentElement,add=doc.addEventListener?"addEventListener":"attachEvent",rem=doc.addEventListener?"removeEventListener":"detachEvent",pre=doc.addEventListener?"":"on",init=function(e){if(e.type==="readystatechange"&&doc.readyState!=="complete"){return}(e.type==="load"?window:doc)[rem](pre+e.type,init,false);if(!done&&(done=true)){callback.call(window,e.type||e)}},poll=function(){try{root.doScroll("left")}catch(e){setTimeout(poll,50);return}init("poll")};if(doc.readyState==="complete"){callback.call(window,"lazy")}else{if(doc.createEventObject&&root.doScroll){try{top=!window.frameElement}catch(e){}if(top){poll()}}doc[add](pre+"DOMContentLoaded",init,false);doc[add](pre+"readystatechange",init,false);window[add](pre+"load",init,false)}},require=function(assets){if(typeof assets==="string"){assets=[assets]}required=required.concat(assets)};
        //]]>
        </script>
        <!--[if lt IE 9]>
        <script src="/static/js/vendors/html5.js"></script>
        <![endif]-->
        <style type="text/css">
            .container {
                padding: 60px 0 0;
            }
            .focus {
                /*color: red !important;*/
            }
            .focus span {
                color: red;
            }
            .hovered {
                position: absolute;
                width: 10px;
                height: 10px;
                background-color: red;
                border-radius: 10px;
                margin: 4px 0 0;
            }
            pre {
                overflow: auto;
                word-wrap: normal;
                white-space: pre;
            }
            footer {
                text-align: right;
            }
            footer span {
                padding: 0 0 0 24px;
            }
            .prettyprint.gutter {
              -webkit-box-shadow: inset 40px 0 0 #fbfbfc, inset 41px 0 0 #ececf0;
                 -moz-box-shadow: inset 40px 0 0 #fbfbfc, inset 41px 0 0 #ececf0;
                      box-shadow: inset 40px 0 0 #fbfbfc, inset 41px 0 0 #ececf0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="span8 offset2">
                    <p class="lead"><?= ($message) ?></p>
<?php
    foreach ($blocks as $block):
?>
                    <div class="block">
                        <h4><?= ($block['path']) ?>:<?= ($block['line']) ?></h4>
                        <pre class="prettyprint gutter linenums:<?= ($block['start']) ?>"><?= ($block['output']) ?></pre>
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
                    <span><?= ($_SERVER['HTTP_HOST']) ?></span>
                </footer>
            </div>
            <script type="text/javascript">
                queue.push(function() {
                    prettyPrint();
                    var blocks = $('.block');
                    jQuery.each(blocks, function(index, block) {

                        var first = $(block).find('li[value]'),
                            lines = first.nextAll(),
                            previous = first.val();
                        first.attr('data-line', first.val());
                        jQuery.each(lines, function(index, line) {
                            $(line).attr('data-line', previous + 1);
                            ++previous;
                        });

                        // selected line
                        var selected = $(block).find('li[data-line="<?= ($blocks[0]['line']) ?>"]');
                        selected.addClass('focus');

                        // create a hovered-focus element for the errored-line
                        var hovered = $('<div class="hovered"></div>'),
                            position = selected.position();
                        hovered.css({
                            left: position.left - 57,
                            top: position.top
                        });
                        $(document.body).append(hovered);
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
                        'http://twitter.github.com/bootstrap/assets/js/google-code-prettify/prettify.js',
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
