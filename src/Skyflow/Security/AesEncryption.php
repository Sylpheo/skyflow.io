<?php

/**
 * AES encryption class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Security;

use Skyflow\Security\EncryptionInterface;

/**
 * AES encryption class.
 */
class AesEncryption implements EncryptionInterface
{
    /**
     * The key with which the data will be encrypted.
     *
     * @var string
     */
    private $key;

    /**
     * AES encryption class constructor.
     *
     * @param string $key The key with which the data will be encrypted.
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Get the key with which the data will be encrypted.
     *
     * @return string The encryption key.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($dataPlain, $salt)
    {
        if ($dataPlain === null) {
            return null;
        } else {
            $key_str           = substr_replace($this->getKey(), $salt, 0, strlen($salt));
            $key               = pack('H*', $key_str);
            $iv_size           = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
            $iv                = mcrypt_create_iv($iv_size, MCRYPT_RAND);

            $ciphertext        = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $dataPlain, MCRYPT_MODE_CBC, $iv);

            $ciphertext        = $iv . $ciphertext;
            $ciphertext_base64 = base64_encode($ciphertext);

            return $ciphertext_base64;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($dataCrypt, $salt)
    {
        if ($dataCrypt === null) {
            return null;
        } else {
            $ciphertext_dec = base64_decode($dataCrypt);
            $iv_size        = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
            $iv_dec         = substr($ciphertext_dec, 0, $iv_size);
            $ciphertext_dec = substr($ciphertext_dec, $iv_size);
            $key_str        = substr_replace($this->getKey(), $salt, 0, strlen($salt));
            $key            = pack('H*', $key_str);
            $plaintext_dec  = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
            $plaintext = rtrim($plaintext_dec, "\0");

            return $plaintext;
        }
    }

    /**
     * Create a random salt.
     *
     * @return string A random salt.
     */
    public function createSalt()
    {
        $randString   = "";
        $charUniverse = "abcdef0123456789";
        for ($i = 0; $i < 64; $i++) {
            $randInt    = rand(0, 15);
            $randChar   = $charUniverse[$randInt];
            $randString = $randString . $randChar;
        }

        return $randString;
    }
}
