<?php

namespace TrkLife\Entity;

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
class Entity
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
     * Called before persisting entity
     *
     * @PrePersist
     */
    public function onPrePersist()
    {
        $this->created = time();
        $this->modified = time();
    }

    /**
     * Called before updating entity
     *
     * @PreUpdate
     */
    public function onPreUpdate()
    {
        $this->modified = time();
    }
}
