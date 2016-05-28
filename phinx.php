<?php
/**
 * Phinx migration configuration
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */

require_once __DIR__ . '/app/config/bootstrap.php';

use TrkLife\Config;

return array(
    'paths' => array(
        'migrations' => '%%PHINX_CONFIG_DIR%%/app/db/migrations'
    ),
    'environments' => array(
        'default_migration_table' => 'phinxlog',
        'default_database' => 'application',
        'application' => array(
            'adapter' => 'mysql',
            'host' => Config::get('Database.host'),
            'name' => Config::get('Database.database'),
            'user' => Config::get('Database.user'),
            'pass' => Config::get('Database.password'),
            'port' => Config::get('Database.port'),
            'charset' => 'utf8'
        ),
        'testing' => array(
            'adapter' => 'mysql',
            'host' => Config::get('Database.host'),
            'name' => Config::get('Database.testDatabase'),
            'user' => Config::get('Database.user'),
            'pass' => Config::get('Database.password'),
            'port' => Config::get('Database.port'),
            'charset' => 'utf8'
        )
    )
);
