<?php

/**
 * DAO class for the Users domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Skyflow\Domain\Users;

/**
 * DAO class for the Users domain object.
 */
class UsersDAO extends DAO implements UserProviderInterface {

    /**
     * Find a User from its id.
     *
     * @param string $id The User's id.
     * @return Users The found User.
     * @throws \Exception if no user found.
     */
    public function find($id) {
        $sql = "select * from users where id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No user matching id " . $id);
    }

    /**
     * Find a User from its skyflow-token.
     *
     * @param $skyflowToken The User's skyflow-token.
     * @return Users The found User.
     */
    public function findByToken($skyflowToken) {
        $sql = "select * from users where skyflowtoken=?";
        $row = $this->getDb()->fetchAssoc($sql, array($skyflowToken));

        if ($row) {
            return $this->buildDomainObject($row);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $sql = "select * from users where username=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Save a User.
     *
     * @param Users $user The User to save.
     */
    public function save(Users $user) {
        $userData = array(
            'username' => $user->getUsername(),
            'salt' => $user->getSalt(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'clientid' => $user->getClientid(),
            'clientsecret' => $user->getClientsecret(),
            'waveid' => $user->getWaveid(),
            'wavesecret' => $user->getWavesecret(),
            'wavelogin' => $user->getWavelogin(),
            'wavepassword' => $user->getWavepassword(),
            'skyflowtoken' => $user->getSkyflowtoken(),
            'access_token_salesforce' => $user->getAccessTokenSalesforce(),
            'refresh_token_salesforce' => $user->getRefreshTokenSalesforce(),
            'instance_url_salesforce' => $user->getInstanceUrlSalesforce(),
            'salesforce_id' => $user->getSalesforceId(),
            'salesforce_secret' => $user->getSalesforceSecret()
        );

        if ($user->getId()) {
            // The user has already been saved : update it
            $this->getDb()->update('users', $userData, array('id' => $user->getId()));
        } else {
            // The user has never been saved : insert it
            $this->getDb()->insert('users', $userData);
            // Get the id of the newly created user and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return 'Skyflow\Domain\Users' === $class;
    }

    /**
     * Creates a User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return Users
     */
    protected function buildDomainObject($row) {
        $user = new Users();
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        $user->setPassword($row['password']);
        $user->setSalt($row['salt']);
        $user->setRole($row['role']);
        $user->setClientid($row['clientid']);
        $user->setClientsecret($row['clientsecret']);
        $user->setWaveid($row['waveid']);
        $user->setWavesecret($row['wavesecret']);
        $user->setWavelogin($row['wavelogin']);
        $user->setWavepassword($row['wavepassword']);
        $user->setSkyflowtoken($row['skyflowtoken']);
        $user->setAccessTokenSalesforce($row['access_token_salesforce']);
        $user->setRefreshTokenSalesforce($row['refresh_token_salesforce']);
        $user->setInstanceUrlSalesforce($row['instance_url_salesforce']);
        $user->setSalesforceId(($row['salesforce_id']));
        $user->setSalesforceSecret($row['salesforce_secret']);
        return $user;
    }
}