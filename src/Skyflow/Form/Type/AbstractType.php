<?php

/**
 * Abstract form type class for Skyflow form types.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Form\Type;

use Symfony\Component\Form\AbstractType as BaseAbstractType;

use Skyflow\Form\Type\TypeInterface;

/**
 * Abstract form type class for Skyflow form types.
 *
 * This form type is abstract because it does not contain any field.
 */
abstract class AbstractType extends BaseAbstractType implements TypeInterface
{
    /**
     * The form type name.
     *
     * @var string
     */
    private $name;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
