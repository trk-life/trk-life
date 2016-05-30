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
     * @return int  Created unix timestamp of the entity
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $created  Created unix timestamp of the entity
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return int  Modified unix timestamp of the entity
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param int $modified Modified unix timestamp of the entity
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
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
