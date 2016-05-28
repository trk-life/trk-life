<?php

namespace TrkLife;

use Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 *
 * Manages configuration of the application
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class Config
{
    /**
     * Stores all of the configuration key value pairs.
     *
     * @var array
     */
    private static $config = array();

    /**
     * Get a config value with specified key
     *
     * @param $key string   the key of the config value
     * @return mixed        the config value
     * @throws Exception    throws an exception if the value doesn't exist
     */
    public static function get($key)
    {
        if (!isset(static::$config[$key])) {
            throw new Exception('Missing configuration value: ' . $key);
        }
        return static::$config[$key];
    }

    /**
     * Set a config value. If an array is passed for the value, multiple values are set.
     *
     * @param $key string   the key of the config value
     * @param $value mixed  the config value or array of values
     * @return void
     */
    public static function set($key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $key_inner => $value_inner) {
                static::set("$key.$key_inner", $value_inner);
            }
        } elseif ($value !== null) {
            static::$config[$key] = $value;
        }
    }

    /**
     * Load a config array
     *
     * @param array $config An array of config values
     * @throws Exception    If config class for environment doesn't exist
     */
    public static function load($config)
    {
        foreach ($config as $key => $value) {
            static::set($key, $value);
        }
    }

    /**
     * Load a yaml config file
     *
     * @param string $path  The path to the .yml file
     * @throws Exception    If file doesn't exist
     */
    public static function loadYaml($path)
    {
        if (!file_exists($path)) {
            throw new Exception('Config file does not exist: ' . $path);
        }

        $config = Yaml::parse(file_get_contents($path));

        if (!empty($config)) {
            static::load($config);
        }
    }

    /**
     * Clears all config values
     *
     * @return void
     */
    public static function clearAll()
    {
        static::$config = array();
    }
}
