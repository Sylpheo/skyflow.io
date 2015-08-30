<?php

/**
 * DAO class for the Skyflow user.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use skyflow\DAO\AbstractDAO;
use skyflow\Domain\AbstractModel;
use skyflow\Domain\SkyflowUser;

/**
 * DAO class for the Skyflow user.
 */
class SkyflowUserDAO extends AbstractDAO implements UserProviderInterface {

    /**
     * Find a User from its id.
     *
     * @param string $id The User's id.
     * @return SkyflowUser The found User.
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
     * @return SkyflowUser The found User.
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
     * Get user data formatted for storage in database.
     *
     * It returns an associative array where the key is the
     * field name in database and the value is the current value
     * in the application.
     *
     * @param  AbstractModel $user The user to get data from.
     * @return array                The user data formatted for storage.
     */
    public function getData(AbstractModel $user)
    {
        return array(
            'username' => $user->getUsername(),
            'salt' => $user->getSalt(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'skyflowtoken' => $user->getSkyflowtoken()
        );
    }

    /**
     * Save a User.
     *
     * This is an upsert. If the user already exists (it has an id)
     * then it is updated, or it is inserted.
     *
     * @param SkyflowUser $user The User to save.
     */
    public function save(SkyflowUser $user) {
        $userData = $this->getData($user);

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
        return 'skyflow\Domain\SkyflowUser' === $class;
    }

    /**
     * Creates a User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return SkyflowUser
     */
    protected function buildDomainObject($row) {
        $user = new SkyflowUser();
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        $user->setPassword($row['password']);
        $user->setSalt($row['salt']);
        $user->setRole($row['role']);
        $user->setSkyflowtoken($row['skyflowtoken']);
        return $user;
    }
}