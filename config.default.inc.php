<?php

    /**
     * Plugin Config Data
     * 
     */
    $logErrors = true;
    $maxNumberOfLines = 10;
    $renderView = true;
    $skin = 'sunburst';
    $template = 'archived';
    $args = array('logErrors', 'maxNumberOfLines', 'renderView', 'skin', 'template');
    $pluginConfigData = compact(... $args);

    /**
     * Storage
     * 
     */
    $key = 'TurtlePHP-ErrorPlugin';
    TurtlePHP\Plugin\Config::set($key, $pluginConfigData);
