<?php

    /**
     * Namespace
     * 
     */
    namespace Plugin\Error;

    /**
     * Plugin Config Data
     * 
     */
    $maxNumberOfLines = 10;
    $skin = 'sunburst';
    $template = 'archived';
    $pluginConfigData = compact('maxNumberOfLines', 'skin', 'template');

    /**
     * Storage
     * 
     */
    $key = 'TurtlePHP-ErrorPlugin';
    \Plugin\Config::add($key, $pluginConfigData);
