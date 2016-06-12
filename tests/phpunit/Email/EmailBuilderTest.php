<?php

namespace TrkLife\Email;

use TrkLife\Config;
use TrkLife\Container;

/**
 * Class EmailBuilderTest
 *
 * @package TrkLife\Email
 * @author George Webb <george@webb.uno>
 */
class EmailBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailBuilder
     */
    public $builder;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        // Required config values
        Config::set('AppDir', dirname(__DIR__) . '/data/email');

        $this->builder = new EmailBuilder(new Container(), 'test_email_with_data', array(
            'data1' => 'Data One',
            'data2' => 'Data Two'
        ));
    }

    public function testBuildHtml()
    {
        $this->assertEquals('TEST HTML - Data One - Data Two',$this->builder->buildHtml());
    }

    public function testBuildText()
    {
        $this->assertEquals('TEST TEXT - Data One - Data Two', $this->builder->buildText());
    }
}
