<?php

/**
 * Event Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Domain;

use Skyflow\Domain\AbstractUserOwnedModel;

/**
 * Event Domain Object class.
 */
class Event extends AbstractUserOwnedModel
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
}
