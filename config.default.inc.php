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
    $pluginConfigData = compact('maxNumberOfLines');

    /**
     * Storage
     * 
     */
    $key = 'TurtlePHP-ErrorPlugin';
    \Plugin\Config::add($key, $pluginConfigData);
