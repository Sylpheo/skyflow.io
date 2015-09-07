<?php

/**
 * Trait for classes that use encryption.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Security;

use skyflow\Security\EncryptionInterface;

/**
 * Trait for classes that use encryption.
 */
trait EncryptionTrait
{
    /**
     * Object used for encryption.
     *
     * @var EncryptionInterface
     */
    private $encryption;

    /**
     * Set the object used for encryption.
     *
     * @param EncryptionInterface $encryption The encryption object.
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * Get the object used for encryption.
     *
     * @return EncryptionInterface The encryption object.
     */
    protected function getEncryption()
    {
        return $this->encryption;
    }
}
