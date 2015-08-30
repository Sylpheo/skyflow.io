<?php

/**
 * Event Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Domain;

use skyflow\Domain\AbstractDomainObject;

/**
 * Event Domain Object class.
 */
class Event extends AbstractDomainObject
{
    /**
     * The Event name.
     *
     * @var string
     */
    private $name;

    /**
     * The Event description.
     *
     * @var string
     */
    private $description;

    /**
    * The id of the User who owns the Event.
    *
    * @var string
    */
    private $userId;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'event';
    }

    /**
     * Get the Event name.
     *
     * @return string The Event name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the Event name.
     *
     * @param string $name The Event name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the Event description.
     *
     * @return string The Event description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the Event description.
     *
     * @param string $description The Event description.
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the id of the User who owns the Event.
     *
     * @return string The id of the User who owns the Event.
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the id of the User who Owns the Event.
     *
     * @param string $id The id of the User who owns the Event.
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}