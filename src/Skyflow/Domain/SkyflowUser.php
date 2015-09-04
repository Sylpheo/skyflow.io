<?php

/**
 * Class for the Skyflow user.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

use skyflow\Domain\AbstractModel;

/**
 * The Skyflow user.
 */
class SkyflowUser extends AbstractModel implements UserInterface
{
    /**
     * User username.
     *
     * @var string
     */
    private $username;

    /**
     * User password.
     *
     * @var string
     */
    private $password;

    /**
     * Salt that was originally used to encode the password.
     *
     * @var string
     */
    private $salt;

    /**
     * Role.
     * Values : ROLE_USER or ROLE_ADMIN.
     *
     * @var string
     */
    private $role;

    /**
     * skyflow token (auto generated).
     *
     * @var string
     */
    private $skyflowtoken;

    /**
     * ExactTarget client id.
     *
     * @var string
     * @todo Delete this.
     */
    private $clientId;

    /**
     * ExactTarget client secret.
     *
     * @var string
     * @todo Delete this.
     */
    private $clientSecret;

    /**
     * Set ExactTarget client id.
     *
     * @param string $clientId The ExactTarget client id.
     * @todo Delete this.
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Get ExactTarget client id.
     *
     * @return string The ExactTarget client id.
     * @todo Delete this.
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set ExactTarget client secret.
     *
     * @param string $clientSecret The ExactTarget client secret.
     * @todo Delete this.
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get ExactTarget client secret.
     *
     * @return string The ExactTarget client secret.
     * @todo Delete this.
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'user';
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set user username.
     *
     * @param string $username The user username.
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getSkyflowtoken()
    {
        return $this->skyflowtoken;
    }

    public function setSkyflowtoken($skyflowtoken)
    {
        $this->skyflowtoken = $skyflowtoken;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // Nothing to do here
    }
}
