<?php

namespace TrkLife\Entity;
use TrkLife\Exception\ValidationException;

/**
 * Class ForgottenPasswordTest
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class ForgottenPasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ForgottenPassword
     */
    private $forgotten_password;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        $this->forgotten_password = new ForgottenPassword();
    }

    public function testSetToken()
    {
        // Test that the token is hashed
        $this->forgotten_password->set('token', 'ok');
        $this->assertNotEquals('ok', $this->forgotten_password->get('token'));

        // Test that setting (therefore hashing) the same token twice gives the same result
        $token = '123';
        $this->forgotten_password->set('token', $token);
        $hashed_token_1 = $this->forgotten_password->get('token');
        $this->forgotten_password->set('token', $token);
        $hashed_token_2 = $this->forgotten_password->get('token');
        $this->assertEquals($hashed_token_1, $hashed_token_2);

        // Test return value (should be false for empty tokens)
        $this->assertTrue($this->forgotten_password->set('token', 'ok'));
        $this->assertFalse($this->forgotten_password->set('token', ''));
        $this->assertFalse($this->forgotten_password->set('token', null));
    }

    public function testHashToken()
    {
        $token = 'mytoken';
        $this->assertNotEquals($token, ForgottenPassword::hashToken($token));
        $this->assertNotEmpty(ForgottenPassword::hashToken($token));

        // Check two hashes give same value
        $this->assertEquals(ForgottenPassword::hashToken($token), ForgottenPassword::hashToken($token));
    }

    public function testGenerateToken()
    {
        $this->assertEquals(64, strlen($this->forgotten_password->generateToken()));
        $this->assertNotEquals($this->forgotten_password->generateToken(), $this->forgotten_password->generateToken());
    }

    public function testGetAttributes()
    {
        $token = $this->forgotten_password->generateToken();

        $this->forgotten_password->set('email', 'george@webb.uno');
        $this->forgotten_password->set('token', $token);
        $this->forgotten_password->set('ip_address', '0.0.0.0');
        $this->forgotten_password->set('user_agent', 'Big Dave\'s Browser V1');
        $this->forgotten_password->set('status', ForgottenPassword::STATUS_RATE_LIMITED);

        $this->assertEquals(
            array(
                'id' => null,
                'email' => 'george@webb.uno',
                'token' => ForgottenPassword::hashToken($token),
                'ip_address' => '0.0.0.0',
                'user_agent' => 'Big Dave\'s Browser V1',
                'status' => ForgottenPassword::STATUS_RATE_LIMITED,
                'created' => null,
                'modified' => null
            ),
            $this->forgotten_password->getAttributes()
        );
    }

    public function testValidate()
    {
        // Test all fail
        $this->forgotten_password->set('id', 'abc');
        $this->forgotten_password->set('ip_address', false);
        $this->forgotten_password->set('user_agent', false);

        try {
            $this->forgotten_password->validate();
            $this->fail('ForgottenPassword should fail to validate with no values set');
        } catch (ValidationException $e) {
            $this->assertEquals(
                array(
                    'ID is invalid.',
                    'Email address is invalid.',
                    'IP Address is invalid.',
                    'User Agent is invalid.',
                    'Status is not valid.'
                ),
                $e->validation_messages
            );
        }

        $this->forgotten_password->set('id', 213);
        $this->forgotten_password->set('email', 'georgeATwebb.uno');
        $this->forgotten_password->set('token', $this->forgotten_password->generateToken());
        $this->forgotten_password->set('ip_address', '0.0.0.0');
        $this->forgotten_password->set('user_agent', 'Big Dave\'s Browser V1');
        $this->forgotten_password->set('status', ForgottenPassword::STATUS_RATE_LIMITED);

        // Test single failure
        try {
            $this->forgotten_password->validate();
            $this->fail('ForgottenPassword should fail to validate with no values set');
        } catch (ValidationException $e) {
            $this->assertEquals(
                array(
                    'Email address is invalid.'
                ),
                $e->validation_messages
            );
        }

        // Test all pass
        $this->forgotten_password->set('email', 'george@webb.uno');

        try {
            $this->forgotten_password->validate();
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('ForgottenPassword should validate successfully.');
        }
    }
}
