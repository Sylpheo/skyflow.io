<?php

/**
 * Mapping Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Domain;

use skyflow\Domain\AbstractDomainObject;

/**
 * Mapping Domain Object class.
 */
class Mapping extends AbstractDomainObject
{
    /**
     * The Event mapped by this Mapping.
     *
     * @var Event
     */
    private $event;

    /**
     * The Flow mapped by this Mapping.
     *
     * @var Flow
     */
    private $flow;

    /**
     * The id of the User who owns this Mapping.
     *
     * @var string
     */
    private $userId;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'mapping';
    }

    /**
     * Get the Event mapped by this Mapping.
     *
     * @return Event The Event.
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set the Event mapped by this Mapping.
     *
     * @param Event $event The Event.
     */
    public function setEvent(Event $event)
    {
        $this->event =$event;
    }

    /**
     * Get the Flow mapped by this Mapping.
     *
     * @return Flow The Flow.
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * Set the Flow mapped by this Mapping.
     *
     * @param Flow $flow The Flow.
     */
    public function setFlow(Flow $flow)
    {
        $this->flow = $flow;
    }

    /**
     * Get the id of the User who owns this Mapping.
     *
     * @return string The id of the User.
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the id of the User who owns this Mapping.
     *
     * @param string $id The id fo the User.
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }
}
