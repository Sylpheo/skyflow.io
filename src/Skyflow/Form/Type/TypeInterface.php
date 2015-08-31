<?php

/**
 * Form type interface for use by the Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Form\Type;

use Symfony\Component\Form\FormTypeInterface;

/**
 * Form type interface for use by the Skyflow addons.
 */
interface TypeInterface extends FormTypeInterface
{
    /**
     * Set the name of this type.
     *
     * @param string $name The name of this type.
     */
    public function setName($name);
}
