<?php

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
    TurtlePHP\Plugin\Config::set($key, $pluginConfigData);
