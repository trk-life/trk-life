<?php

namespace TrkLife\Entity;

use TrkLife\Exception\ValidationException;

/**
 * Class Entity
 *
 * Base entity class, containing common functionality for entities
 *
 * @package TrkLife\Entity
 * @author George Webb <george@webb.uno>
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
abstract class Entity
{
    /**
     * Created unix timestamp of the entity
     *
     * @Column(type="integer")
     * @var int
     */
    protected $created;

    /**
     * Modified unix timestamp of the entity
     *
     * @Column(type="integer")
     * @var int
     */
    protected $modified;

    /**
     * General purpose getter
     *
     * @param string $key   The name of the param to get
     * @return mixed        The value of the param
     * @throws \Exception   If the param doesn't exist
     */
    public function get($key)
    {
        $get_method_name = "get" . static::getParamForMethodName($key);
        if (method_exists($this, $get_method_name)) {
            return $this->$get_method_name();
        }

        if (!property_exists($this, $key)) {
            throw new \Exception("Parameter '$key' missing.");
        }

        return $this->$key;
    }

    /**
     * General purpose setter
     *
     * @param string $key   The name of the param to set
     * @param mixed $value  The value to set
     * @return bool         If the set was successful
     * @throws \Exception   If the param doesn't exist
     */
    public function set($key, $value)
    {
        $set_method_name = "set" . static::getParamForMethodName($key);
        if (method_exists($this, $set_method_name)) {
            return $this->$set_method_name($value);
        }

        if (!property_exists($this, $key)) {
            throw new \Exception("Parameter '$key' missing.");
        }

        $this->$key = $value;
        return true;
    }

    /**
     * Turns a param name with underscores to the form required for getters and setters
     * i.e. turn first_name to FirstName, for getFirstName and setFirstName
     *
     * @param string $param_name    The param name
     * @return string               The formatted param name for method names
     */
    public static function getParamForMethodName($param_name)
    {
        // Replace underscores and hyphens for spaces
        $param_name = str_replace(array('_', '-'), ' ', $param_name);

        // UC first char of each word
        $param_name = ucwords(strtolower($param_name));

        // Remove spaces
        $param_name = str_replace(' ', '', $param_name);

        return $param_name;
    }

    /**
     * Returns the entity's attributes
     *
     * @return array    The array of attributes
     */
    abstract public function getAttributes();

    /**
     * Called before persisting entity
     *
     * @PrePersist
     */
    final public function onPrePersist()
    {
        $this->created = time();
        $this->modified = time();
        $this->prePersistHook();
        $this->validate();
    }

    /**
     * Called before updating entity
     *
     * @PreUpdate
     */
    final public function onPreUpdate()
    {
        $this->modified = time();
        $this->preUpdateHook();
        $this->validate();
    }

    /**
     * Called pre-persisting
     */
    protected function prePersistHook()
    {
        // Overridden in entity where necessary
    }

    /**
     * Called pre-update
     */
    protected function preUpdateHook()
    {
        // Overridden in entity where necessary
    }

    /**
     * Validate the fields before persisting entity or updating entity
     *
     * @throws ValidationException
     */
    abstract public function validate();
}
