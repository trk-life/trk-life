<?php

namespace TrkLife;

/**
 * Class ConfigTest
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Re-add config as it is needed elsewhere
     */
    public static function tearDownAfterClass()
    {
        Config::clearAll();
        Config::loadYaml(__DIR__ . '/data/config.yml');
    }

    /**
     * Tests that flat config key value pairs can be set and get correctly.
     */
    public function testGetSetFlat()
    {
        $expected = array(
            'string' => 'testing string',
            'int' => 1,
            'int0' => 0
        );

        foreach ($expected as $key => $value) {
            Config::set($key, $value);
            $actual = Config::get($key);
            $this->assertEquals($value, $actual);
        }
    }

    /**
     * Tests that multidimensional config key value pairs can be set and gotten correctly.
     */
    public function testGetSetRecursive()
    {
        $input = array(
            'Top' => array(
                'Middle' => array(
                    'Bottom' => 'testVal1'
                ),
                'MidVal' => 'testVal2'
            ),
            'TopVal' => 'testVal3'
        );

        $expected = array(
            'App.Top.Middle.Bottom' => 'testVal1',
            'App.Top.MidVal' => 'testVal2',
            'App.TopVal' => 'testVal3'
        );

        Config::set('App', $input);
        foreach ($expected as $key => $value) {
            $actual = Config::get($key);
            $this->assertEquals($value, $actual);
        }
    }

    /**
     * Tests that the get method throws an exception when a config value doesn't exist.
     *
     * @throws \Exception
     */
    public function testGetThrowsException()
    {
        $this->setExpectedException('\Exception');
        Config::get(md5(time()));
    }

    /**
     * Test that set ignores null values
     *
     * @throws \Exception
     */
    public function testSetIgnoresNullValues()
    {
        $this->setExpectedException('\Exception');
        Config::set('myNullValue', null);
        Config::get('myNullValue');
    }

    /**
     * Tests that the load function loads in config values
     */
    public function testLoad()
    {
        Config::load(array(
            'App' => array(
                'one' => 1,
                'two' => 2,
                'three' => 3,
            ),
            'Test' => 'test'
        ));

        $expected = array(
            'App.one' => 1,
            'App.two' => 2,
            'App.three' => 3,
            'Test' => 'test',
        );

        foreach ($expected as $key => $value) {
            $actual = Config::get($key);
            $this->assertEquals($value, $actual);
        }
    }

    /**
     * Test loading of a yaml file for configuration
     *
     * @throws \Exception
     */
    public function testLoadYaml()
    {
        Config::loadYaml(__DIR__ . '/data/test_config.yml');

        $expected = array(
            'myValue' => 'oki doki',
            'anotherval.one' => 'ok',
            'anotherval.two' => 'okok',
            'anotherval.three' => 'okokok'
        );

        $not_expected = array(
            'anotherval.four' => null,
            'anotherval.five' => null,
            'anotherval.six' => null
        );

        foreach ($expected as $key => $value) {
            $actual = Config::get($key);
            $this->assertEquals($value, $actual);
        }

        foreach ($not_expected as $key => $value) {
            try {
                Config::get($key);
                $this->assertTrue(false);
            } catch (\Exception $e) {
                $this->assertContains('Missing configuration value', $e->getMessage());
            }
        }
    }

    /**
     * Test that loading a yaml file which doesnt exist throws the correct exception.
     *
     * @throws \Exception
     */
    public function testLoadYamlThrowsExceptionsOnMissingFile()
    {
        $this->setExpectedException('\Exception');
        Config::loadYaml(__DIR__ . '/data/test_config_non_existant.yml');
    }

    /**
     * Test that the clear all function clears out all config
     *
     * @throws \Exception
     */
    public function testClearAll()
    {
        $this->setExpectedException('\Exception');

        Config::set('Test', 'test');
        $this->assertEquals('test', Config::get('Test'));
        Config::clearAll();
        Config::get('Test');
    }
}
