<?php

namespace TrkLife\Entity;

use TrkLife\Exception\ValidationException;
use TrkLife\Validator;

/**
 * ForgottenPassword entity
 *
 * @package TrkLife\Entity
 * @author George Webb <george@webb.uno>
 *
 * @Entity @Table(name="forgotten_password_requests")
 */
class ForgottenPassword extends Entity
{
    /**
     * The status of a request which has been submitted
     */
    const STATUS_SUBMITTED = 'submitted';

    /**
     * The status of a request which has been used
     */
    const STATUS_USED = 'used';

    /**
     * The status of a request which had an invalid email
     */
    const STATUS_INVALID_EMAIL = 'invalid-email';

    /**
     * The status of a request which has been rate limited
     */
    const STATUS_RATE_LIMITED = 'rate-limited';

    /**
     * Request's ID
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * Email address requested
     *
     * @Column(type="string")
     * @var string
     */
    protected $email;

    /**
     * The forgotten password token
     *
     * @Column(type="string")
     * @var string
     */
    protected $token;

    /**
     * IP address of request
     *
     * @Column(type="string")
     * @var string
     */
    protected $ip_address;

    /**
     * User agent of request
     *
     * @Column(type="string")
     * @var string
     */
    protected $user_agent;

    /**
     * Status of the request
     *
     * @Column(type="string")
     * @var string
     */
    protected $status;

    /**
     * Generates and returns a token
     *
     * @return string   A random token
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
            'email' => $this->email,
            'token' => $this->token,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'status' => $this->status,
            'created' => $this->created,
            'modified' => $this->modified
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

        // Email
        if (!Validator::validateField($this->email, 'email', array('notEmpty' => array(), 'required' => true))) {
            $messages[] = 'Email address is invalid.';
        }

        // Token
        if (!Validator::validateField($this->token, 'stringType', array('notEmpty' => array()))) {
            $messages[] = 'Token is invalid.';
        }

        // IP Address
        if (!Validator::validateField($this->ip_address, 'stringType', array('notEmpty' => array()))) {
            $messages[] = 'IP Address is invalid.';
        }

        // User agent
        if (!Validator::validateField($this->user_agent, 'stringType', array('notEmpty' => array()))) {
            $messages[] = 'User Agent is invalid.';
        }

        // Status
        if (!Validator::validateField($this->status, 'slug', array(
            'notEmpty' => array(),
            'length' => array(1, 20),
            'required' => true
        ))) {
            $messages[] = 'Status is not valid.';
        }

        if (!empty($messages)) {
            throw new ValidationException($messages);
        }
    }
}
