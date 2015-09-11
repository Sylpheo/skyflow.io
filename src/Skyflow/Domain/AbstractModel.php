<?php

/**
 * Abstract domain object that all domain classes must extend.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Domain;

use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;

abstract class AbstractModel implements ObjectIdentityInterface
{
    /**
     * The domain object id.
     *
     * @var string
     */
    private $id;

    /**
     * Get the domain object id.
     *
     * This is an alias for getIdentifier().
     *
     * @return string The domain object id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the domain object id.
     *
     * @param string $id The domain object id.
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ObjectIdentityInterface $identity)
    {
        return $this->getIdentifier() === $identity->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getType();
}
