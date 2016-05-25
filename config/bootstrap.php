<?php

/**
 * Bootstrap application by setting up autoloading and configuration
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('UTC');

require_once dirname(__DIR__) . '/vendor/autoload.php';

use TrkLife\Config;

Config::set('RootDir', dirname(__DIR__));

// Load default config
Config::loadYaml(__DIR__ . '/defaults.yml');

// Autoload other config
foreach (scandir(Config::get('RootDir') . '/config') as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'yml') {
        Config::loadYaml(Config::get('RootDir') . '/config/' . $file);
    }
}
