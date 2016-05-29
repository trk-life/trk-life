<?php

namespace TrkLife;

/**
 * Tests validator class
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests successful validation
     */
    public function testValidateFieldSuccess()
    {
        // String
        $this->assertTrue(Validator::validateField('ok', 'stringType'));

        // Int
        $this->assertTrue(Validator::validateField('10', 'intVal'));
        $this->assertTrue(Validator::validateField(42, 'intVal'));

        // Slug
        $this->assertTrue(Validator::validateField('ok-then', 'slug'));

        // Email
        $this->assertTrue(Validator::validateField('george@webb.uno', 'email'));

        // Required
        $this->assertTrue(Validator::validateField('ok', 'stringType', array('required' => true)));
        $this->assertTrue(Validator::validateField('', 'stringType', array('required' => true)));
        $this->assertTrue(Validator::validateField(null, 'stringType', array('required' => false)));

        // Not empty
        $this->assertTrue(Validator::validateField('ok', 'stringType', array('notEmpty' => array())));
        $this->assertTrue(Validator::validateField(1, 'intVal', array('notEmpty' => array())));

        // Length
        $this->assertTrue(Validator::validateField('ok', 'stringType', array('length' => array(0, 2))));
        $this->assertTrue(Validator::validateField('o', 'stringType', array('length' => array(0, 2))));
        $this->assertTrue(Validator::validateField('', 'stringType', array('length' => array(0, 2))));
    }

    /**
     * Test failed validation
     */
    public function testValidateFieldFailure()
    {
        // String
        $this->assertFalse(Validator::validateField(1, 'stringType'));

        // Int
        $this->assertFalse(Validator::validateField('10a', 'intVal'));
        $this->assertFalse(Validator::validateField(true, 'intVal'));

        // Slug
        $this->assertFalse(Validator::validateField('ok_ok', 'slug'));
        $this->assertFalse(Validator::validateField('ok ok', 'slug'));

        // Email
        $this->assertFalse(Validator::validateField('georgewebb.uno', 'email'));
        $this->assertFalse(Validator::validateField('george@webbuno', 'email'));
        $this->assertFalse(Validator::validateField('', 'email'));

        // Required
        $this->assertFalse(Validator::validateField(null, 'stringType', array('required' => true)));

        // Not empty
        $this->assertFalse(Validator::validateField('', 'stringType', array('notEmpty' => array())));
        $this->assertFalse(Validator::validateField(0, 'intVal', array('notEmpty' => array())));

        // Length
        $this->assertFalse(Validator::validateField('okk', 'stringType', array('length' => array(0, 2))));
        $this->assertFalse(Validator::validateField('', 'stringType', array('length' => array(1, 2))));
    }
}
