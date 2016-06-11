<?php

namespace TrkLife\Entity;

use TrkLife\Exception\ValidationException;
use TrkLife\Validator;

/**
 * Token entity
 *
 * @package TrkLife\Entity
 * @author George Webb <george@webb.uno>
 *
 * @Entity(repositoryClass="TrkLife\Entity\TokenRepository")
 * @Table(name="user_tokens")
 */
class Token extends Entity
{
    /**
     * Token's ID
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * The user ID for token's user
     *
     * @Column(type="integer")
     * @var int
     */
    private $user_id;

    /**
     * Hashed token
     *
     * @Column(type="string")
     * @var string
     */
    private $token;

    /**
     * The number of seconds after the creation date that the token is invalid
     *
     * @Column(type="integer")
     * @var int
     */
    private $expires_after;

    /**
     * The unix timestamp of the time the token was last accessed
     *
     * @Column(type="integer")
     * @var int
     */
    private $last_accessed;

    /**
     * The user agent of the browser used to create the token
     *
     * @Column(type="string")
     * @var string
     */
    private $user_agent;

    /**
     * Hashes the given token before setting it
     *
     * @param string $token Hashed token
     */
    protected function setToken($token)
    {
        $this->token = static::hashToken($token);
    }

    /**
     * Hashes a token for storage in the DB
     *
     * @param string $token The token to hash
     * @return string       The sha256 hashed token
     */
    public static function hashToken($token)
    {
        return hash('sha256', $token);
    }

    /**
     * Generates a random token
     *
     * @return string   The token string
     */
    public function generateToken()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * Returns the entity's attributes
     *
     * @return array    The array of attributes
     */
    public function getAttributes()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'token' => $this->token,
            'expires_after' => $this->expires_after,
            'last_accessed' => $this->last_accessed,
            'user_agent' => $this->user_agent,
            'created' => $this->created
        );
    }

    /**
     * Validate the fields before persisting entity or updating entity
     *
     * @throws ValidationException
     */
    public function validate()
    {
        $messages = array();

        // ID
        if (!Validator::validateField($this->id, 'intVal')) {
            $messages[] = 'ID is invalid.';
        }

        // User ID
        if (!Validator::validateField($this->user_id, 'intVal', array('notEmpty' => array(), 'required' => true))) {
            $messages[] = 'User ID is invalid.';
        }

        // Token
        if (!Validator::validateField($this->token, 'stringType', array('notEmpty' => array(), 'required' => true))) {
            $messages[] = 'Token is invalid.';
        }

        // Expires After
        if (!Validator::validateField($this->expires_after, 'intVal', array('required' => true))) {
            $messages[] = 'Expires after is required.';
        }

        // Last Accessed
        if (!Validator::validateField($this->last_accessed, 'intVal', array('required' => true))) {
            $messages[] = 'Last accessed time is required.';
        }

        // User Agent
        if (!Validator::validateField($this->user_agent, 'stringType')) {
            $messages[] = 'User agent is invalid.';
        }

        if (!empty($messages)) {
            throw new ValidationException($messages);
        }
    }

    /**
     * Should not update a token
     *
     * @throws \Exception
     */
    protected function preUpdateHook()
    {
        throw new \Exception('Cannot update token entity.');
    }
}
