<?php

/**
 * Flow Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Domain;

use Skyflow\Domain\AbstractUserOwnedModel;

/**
 * Flow Domain Object class.
 */
class Flow extends AbstractUserOwnedModel
{
    /**
     * The Flow name
     *
     * @var string
     */
    private $name;

    /**
     * The Flow class
     *
     * @var string
     */
    private $class;

    /**
     * The Flow documentation
     *
     * @var string
     */
    private $documentation;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'flow';
    }

    /**
     * Get the Flow name.
     *
     * @return string The Flow name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the Flow name.
     *
     * @param string $name The Flow name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the Flow class.
     *
     * @return string The Flow class.
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the Flow class.
     *
     * @param string $class The Flow class.
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Get the Flow documentation.
     *
     * @return string The Flow documentation.
     */
    public function getDocumentation()
    {
        return $this->documentation;
    }

    /**
     * Set the Flow documentation.
     *
     * @param string $documentation The Flow documentation.
     */
    public function setDocumentation($documentation)
    {
        $this->documentation = $documentation;
    }
}
