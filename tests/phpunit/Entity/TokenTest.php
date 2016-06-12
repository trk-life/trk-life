<?php

namespace TrkLife\Entity;
use TrkLife\Exception\ValidationException;

/**
 * Class TokenTest
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Token
     */
    private $token;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        $this->token = new Token();
    }

    /**
     * test setting of token - make sure it is hashed
     */
    public function testSetToken()
    {
        // Test that the token is hashed
        $this->token->set('token', 'ok');
        $this->assertNotEquals('ok', $this->token->get('token'));

        // Test that setting (therefore hashing) the same token twice gives the same result
        $token = '123';
        $this->token->set('token', $token);
        $hashed_token_1 = $this->token->get('token');
        $this->token->set('token', $token);
        $hashed_token_2 = $this->token->get('token');
        $this->assertEquals($hashed_token_1, $hashed_token_2);

        // Test return value (should be false for empty tokens)
        $this->assertTrue($this->token->set('token', 'ok'));
        $this->assertFalse($this->token->set('token', ''));
        $this->assertFalse($this->token->set('token', null));
    }

    /**
     * Test token hashing
     */
    public function testHashToken()
    {
        $token = 'mytoken';
        $this->assertNotEquals($token, Token::hashToken($token));
        $this->assertNotEmpty(Token::hashToken($token));

        // Check two hashes give same value
        $this->assertEquals(Token::hashToken($token), Token::hashToken($token));
    }

    /**
     * Test token generation
     */
    public function testGenerateToken()
    {
        $this->assertEquals(64, strlen($this->token->generateToken()));
        $this->assertNotEquals($this->token->generateToken(), $this->token->generateToken());
    }

    /**
     * Test get attributes
     */
    public function testGetAttributes()
    {
        $time = time();
        $token = $this->token->generateToken();

        $this->token->set('user_id', 4);
        $this->token->set('token', $token);
        $this->token->set('expires_after', 60 * 60 * 24);
        $this->token->set('last_accessed', $time);
        $this->token->set('user_agent', 'Big Dave\'s Browser V1');

        $this->assertEquals(
            array(
                'id' => null,
                'user_id' => 4,
                'token' => Token::hashToken($token),
                'expires_after' => 60 * 60 * 24,
                'last_accessed' => $time,
                'user_agent' => 'Big Dave\'s Browser V1',
                'created' => null
            ),
            $this->token->getAttributes()
        );
    }

    /**
     * Test validation
     */
    public function testValidate()
    {
        // Test all fail
        try {
            $this->token->validate();
            $this->fail('Token should fail to validate with no values set');
        } catch (ValidationException $e) {
            $this->assertEquals(
                array(
                    'User ID is invalid.',
                    'Token is invalid.',
                    'Expires after is required.',
                    'Last accessed time is required.'
                ),
                $e->validation_messages
            );
        }

        // Test two fails
        $this->token->set('id', 'abc');
        $this->token->set('user_id', 4);
        $this->token->set('token', $this->token->generateToken());
        $this->token->set('expires_after', 60 * 60 * 24);
        $this->token->set('last_accessed', time());
        $this->token->set('user_agent', false);

        try {
            $this->token->validate();
            $this->fail('Token should fail to validate with no values set');
        } catch (ValidationException $e) {
            $this->assertEquals(
                array(
                    'ID is invalid.',
                    'User agent is invalid.'
                ),
                $e->validation_messages
            );
        }

        // Test all pass
        $this->token->set('id', 1);
        $this->token->set('user_agent', 'Big Dave\'s Browser V1');

        try {
            $this->token->validate();
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('Token should validate successfully.');
        }
    }

    public function testPrePersist()
    {
        $this->token->set('id', 1);
        $this->token->set('user_id', 4);
        $this->token->set('token', $this->token->generateToken());
        $this->token->set('expires_after', 60 * 60 * 24);
        $this->token->set('last_accessed', time());
        $this->token->set('user_agent', 'Big Dave\'s Browser V1');

        $this->assertNull($this->token->get('created'));
        $this->assertNull($this->token->get('modified'));

        $this->token->onPrePersist();

        $this->assertTrue(is_int($this->token->get('created')));
        $this->assertTrue(is_int($this->token->get('modified')));
    }

    public function testPreUpdate()
    {
        try {
            $this->token->onPreUpdate();
            $this->fail('Updating token entity must throw an exception');
        } catch (\Exception $e) {
            $this->assertEquals('Cannot update token entity.', $e->getMessage());
        }
    }
}
