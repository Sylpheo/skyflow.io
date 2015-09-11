<?php

/**
 * Interface for encryption classes.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Security;

/**
 * Interface for encryption classes.
 */
interface EncryptionInterface
{
    /**
     * Encrypt plain data.
     *
     * @param  string $dataPlain The plain data to encrypt.
     * @param  string $salt      The salt to use for encryption.
     * @return string            The encrypted data.
     */
    public function encrypt($dataPlain, $salt);

    /**
     * Decrypt encrypted data.
     *
     * @param  string $dataCrypt Encrypted data.
     * @param  string $salt      The salt to use for decryption.
     * @return string            The decrypted data.
     */
    public function decrypt($dataCrypt, $salt);
}
