<?php

namespace TrkLife\Entity;

use TrkLife\Exception\ValidationException;
use TrkLife\Validator;

/**
 * User entity
 *
 * @package TrkLife\Entity
 * @author George Webb <george@webb.uno>
 *
 * @Entity @Table(name="users")
 */
class User extends Entity
{
    /**
     * Admin role
     */
    const ROLE_ADMIN = 'admin';

    /**
     * User role
     */
    const ROLE_USER = 'user';

    /**
     * Active status
     */
    const STATUS_ACTIVE = 'active';

    /**
     * Disabled status
     */
    const STATUS_DISABLED = 'disabled';

    /**
     * User's ID
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * User's email address
     *
     * @Column(type="string")
     * @var string
     */
    protected $email;

    /**
     * User's hashed password
     *
     * @Column(type="string")
     * @var string
     */
    protected $password;

    /**
     * User's first name
     *
     * @Column(type="string")
     * @var string
     */
    protected $first_name;

    /**
     * User's last name
     *
     * @Column(type="string")
     * @var string
     */
    protected $last_name;

    /**
     * User's role
     *
     * @Column(type="string")
     * @var string
     */
    protected $role;

    /**
     * User's status: active or disabled
     *
     * @Column(type="string")
     * @var string
     */
    protected $status;

    /**
     * @param string $password  User's hashed password
     * @return bool             If the password was validated successfully
     */
    protected function setPassword($password)
    {
        // Do password strength validation now, as we are hashing it
        if (!Validator::validateField($password, 'stringType', array('length' => array(8, null)))) {
            return false;
        }

        // If password is empty just set to null, don't try and hash it
        if (empty($password)) {
            $this->password = null;
            return true;
        }

        $this->password = $this->hashPassword($password);
        return true;
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->role,
            'status' => $this->status,
            'created' => $this->created,
            'modified' => $this->modified
        );
    }

    /**
     * Hash password, uses bcrypt hashing algorithm
     *
     * @param $password
     * @return bool|string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Checks a password against the hashed password
     *
     * @param string $plaintext_password    The plaintext password to check
     * @return bool                         Whether or not the password is correct
     */
    public function checkPassword($plaintext_password)
    {
        return password_verify($plaintext_password, $this->password);
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

        // Password - no need to validate as it is validated on set and isn't required for an update

        // First name
        if (!Validator::validateField($this->first_name, 'stringType', array(
            'notEmpty' => array(),
            'length' => array(1, 35),
            'required' => true
        ))) {
            $messages[] = 'First name is required.';
        }

        // Last name
        if (!Validator::validateField($this->last_name, 'stringType', array(
            'notEmpty' => array(),
            'length' => array(1, 35),
            'required' => true
        ))) {
            $messages[] = 'Last name is required.';
        }

        // Role
        if (!Validator::validateField($this->role, 'stringType', array(
            'notEmpty' => array(),
            'length' => array(1, 35),
            'required' => true
        ))) {
            $messages[] = 'Role is required.';
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
