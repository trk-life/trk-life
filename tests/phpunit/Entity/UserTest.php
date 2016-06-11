<?php

namespace TrkLife\Entity;
use TrkLife\Exception\ValidationException;

/**
 * Class UserTest
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        $this->user = new User();
    }

    /**
     * Test the getter function
     */
    public function testGet()
    {
        // Perfect set and get scenario
        $this->user->set('first_name', 'George');
        $this->user->set('status', User::STATUS_ACTIVE);

        $this->assertEquals('George', $this->user->get('first_name'));
        $this->assertEquals(User::STATUS_ACTIVE, $this->user->get('status'));

        // Value not set
        $this->assertNull($this->user->get('last_name'));

        // Non-existent param
        try {
            $this->user->get('missing_key');
            $this->fail('Get should throw exception on missing key');
        } catch (\Exception $e) {
            $this->assertEquals("Parameter 'missing_key' missing.", $e->getMessage());
        }
    }

    /**
     * Test the setter function
     */
    public function testSet()
    {
        // Perfect set and get scenario
        $this->assertTrue($this->user->set('last_name', 'Webb'));
        $this->assertEquals('Webb', $this->user->get('last_name'));

        // Non-existent param
        try {
            $this->user->set('missing_key', 'ok');
            $this->fail('Set should throw exception on missing key');
        } catch (\Exception $e) {
            $this->assertEquals("Parameter 'missing_key' missing.", $e->getMessage());
        }
    }

    /**
     * Test the set password function (overridden setter)
     */
    public function testSetPassword()
    {
        // Test set returns true
        $this->assertTrue($this->user->set('password', '12345678'));

        // Handle invalid password (must be at least 8 chars length)
        $this->assertFalse($this->user->set('password', '1234567'));
        $this->assertFalse($this->user->set('password', ''));

        // Setting as null should set value as null, not hash of nothing
        $this->assertTrue($this->user->set('password', null));
        $this->assertNull($this->user->get('password'));

        // Check get value is not the raw password and is the hash
        $this->user->set('password', 'somelongpassword123');
        $this->assertNotEquals('somelongpassword123', $this->user->get('password'));
        $this->assertNotEmpty($this->user->get('password'));

        // Check hash of same password doesn't match
        $raw_password = '!testingpassword!';
        $this->user->set('password', $raw_password);
        $hash_1 = $this->user->get('password');
        $this->user->set('password', $raw_password);
        $hash_2 = $this->user->get('password');
        $this->assertNotEquals($hash_1, $hash_2);
    }

    /**
     * Test the get attributes function
     */
    public function testGetAttributes()
    {
        $this->user->set('first_name', 'George');
        $this->user->set('last_name', 'Webb');
        $this->user->set('email', 'george@webb.uno');
        $this->user->set('password', '12345678');
        $this->user->set('status', User::STATUS_ACTIVE);
        $this->user->set('role', User::ROLE_ADMIN);

        $this->assertEquals(
            array(
                'id' => null,
                'email' => 'george@webb.uno',
                'first_name' => 'George',
                'last_name' => 'Webb',
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'created' => null,
                'modified' => null
            ),
            $this->user->getAttributes()
        );
    }

    /**
     * Test the hash password function
     */
    public function testHashPassword()
    {
        // Check hash is string and isn't empty
        $this->assertStringStartsWith('$', $this->user->hashPassword('password'));

        // Check 2 hashes of same string don't match
        $hash_1 = $this->user->hashPassword('12345678');
        $hash_2 = $this->user->hashPassword('12345678');
        $this->assertNotEquals($hash_1, $hash_2);
    }

    /**
     * Test the check password function
     */
    public function testCheckPassword()
    {
        $password = '12345678';
        $this->user->set('password', $password);

        // Check correct password
        $this->assertTrue($this->user->checkPassword($password));

        // Check empty and false passwords
        $this->assertFalse($this->user->checkPassword(''));
        $this->assertFalse($this->user->checkPassword('otherpassword'));
    }

    /**
     * Test the validation function
     */
    public function testValidate()
    {
        // Test all failed
        try {
            $this->user->validate();
            $this->fail('User should fail to validate with no values set');
        } catch (ValidationException $e) {
            $this->assertEquals(
                array(
                    'Email address is invalid.',
                    'First name is required.',
                    'Last name is required.',
                    'Role is required.',
                    'Status is not valid.'
                ),
                $e->validation_messages
            );
        }

        // Test one fail
        $this->user->set('first_name', 'George');
        $this->user->set('last_name', 'Webb');
        $this->user->set('email', 'georgeATwebb.uno');
        $this->user->set('password', '12345678');
        $this->user->set('status', User::STATUS_ACTIVE);
        $this->user->set('role', User::ROLE_ADMIN);

        try {
            $this->user->validate();
            $this->fail('User should fail to validate with invalid email.');
        } catch (ValidationException $e) {
            $this->assertEquals(
                array('Email address is invalid.'),
                $e->validation_messages
            );
        }

        // Test pass
        $this->user->set('email', 'george@webb.uno');

        try {
            $this->user->validate();
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('User should validate successfully.');
        }

        // Test id fails
        $this->user->set('id', 'abc');

        try {
            $this->user->validate();
            $this->fail('User should fail to validate with invalid ID.');
        } catch (ValidationException $e) {
            $this->assertEquals(
                array('ID is invalid.'),
                $e->validation_messages
            );
        }
    }

    public function testPrePersist()
    {
        $this->assertNull($this->user->get('created'));
        $this->assertNull($this->user->get('modified'));

        // Test validation will fails
        $this->user->set('first_name', 'George');
        $this->user->set('last_name', 'Webb');
        $this->user->set('email', 'georgeATwebb.uno');
        $this->user->set('password', '12345678');
        $this->user->set('status', User::STATUS_ACTIVE);
        $this->user->set('role', User::ROLE_ADMIN);

        try {
            $this->user->onPrePersist();
            $this->fail('User validation should fail on PreUpdate');
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }

        // Test validation passes
        $this->user->set('email', 'george@webb.uno');

        $this->user->onPrePersist();

        $this->assertTrue(is_int($this->user->get('created')));
        $this->assertTrue(is_int($this->user->get('modified')));
    }

    public function testPreUpdate()
    {
        $this->assertNull($this->user->get('modified'));

        // Test validation will fails
        $this->user->set('first_name', 'George');
        $this->user->set('last_name', 'Webb');
        $this->user->set('email', 'georgeATwebb.uno');
        $this->user->set('password', '12345678');
        $this->user->set('status', User::STATUS_ACTIVE);
        $this->user->set('role', User::ROLE_ADMIN);

        try {
            $this->user->onPreUpdate();
            $this->fail('User validation should fail on PreUpdate');
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }

        // Test validation passes
        $this->user->set('email', 'george@webb.uno');

        $this->user->onPreUpdate();

        $this->assertTrue(is_int($this->user->get('modified')));
    }
}
