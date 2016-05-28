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
    private $id;

    /**
     * User's email address
     *
     * @Column(type="string")
     * @var string
     */
    private $email;

    /**
     * User's hashed password
     *
     * @Column(type="string")
     * @var string
     */
    private $password;

    /**
     * User's first name
     *
     * @Column(type="string")
     * @var string
     */
    private $first_name;

    /**
     * User's last name
     *
     * @Column(type="string")
     * @var string
     */
    private $last_name;

    /**
     * User's role
     *
     * @Column(type="string")
     * @var string
     */
    private $role;

    /**
     * User's status: active or disabled
     *
     * @Column(type="string")
     * @var string
     */
    private $status;

    /**
     * @return int User's ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id   User's ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string   User's email address
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email User's email address
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string   User's hashed password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password  User's hashed password
     */
    public function setPassword($password)
    {
        $this->password = $this->hashPassword($password);
    }

    /**
     * @return string   User's first name
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name    User's first name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string   User's last name
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name User's last name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string   User's role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role  User's role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string   User's status (active, disabled)
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status    User's status (active, disabled)
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * Checks a hashed password
     *
     * @param string $plaintext_password    The plaintext password to check
     * @param string $hashed_password       The hashed password with algorithm and cost prefixed
     * @return bool                         Whether or not the password is correct
     */
    public function checkPassword($plaintext_password, $hashed_password)
    {
        return password_verify($plaintext_password, $hashed_password);
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

        // Password TODO: Validate before hashing
        if (!Validator::validateField($this->password, 'stringType', array(
            'notEmpty' => array(),
            'length' => array(8, null)
        ))) {
            $messages[] = 'Password must be at least 8 characters long.';
        }

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
