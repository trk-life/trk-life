<?php

namespace TrkLife\Entity;

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
     * User's status: active or disabled
     *
     * @Column(type="string")
     * @var string
     */
    private $status;

    // TODO: role

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
     * Validate the fields before persisting entity or updating entity
     *
     * @PrePersist @PreUpdate
     */
    public function validate()
    {
        // TODO
    }
}
