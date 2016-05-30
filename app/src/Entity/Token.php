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
 * @Entity @Table(name="user_tokens")
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
     * @return int  Token's ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id   Token's ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int  The user ID for token's user
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id  The user ID for token's user
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string   Hashed token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Hashes the given token before setting it
     *
     * @param string $token Hashed token
     */
    public function setToken($token)
    {
        $this->token = $this->hashToken($token);
    }

    /**
     * @return int  The number of seconds after the creation date that the token is invalid
     */
    public function getExpiresAfter()
    {
        return $this->expires_after;
    }

    /**
     * @param int $expires_after    The number of seconds after the creation date that the token is invalid
     */
    public function setExpiresAfter($expires_after)
    {
        $this->expires_after = $expires_after;
    }

    /**
     * @return int  The unix timestamp of the time the token was last accessed
     */
    public function getLastAccessed()
    {
        return $this->last_accessed;
    }

    /**
     * @param int $last_accessed    The unix timestamp of the time the token was last accessed
     */
    public function setLastAccessed($last_accessed)
    {
        $this->last_accessed = $last_accessed;
    }

    /**
     * @return string   The user agent of the browser used to create the token
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * @param string $user_agent    The user agent of the browser used to create the token
     */
    public function setUserAgent($user_agent)
    {
        $this->user_agent = $user_agent;
    }

    /**
     * Hashes a token for storage in the DB
     *
     * @param string $token The token to hash
     * @return string       The sha256 hashed token
     */
    public function hashToken($token)
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
