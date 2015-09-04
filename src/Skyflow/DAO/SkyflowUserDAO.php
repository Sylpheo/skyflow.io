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
use Doctrine\DBAL\Connection;

use skyflow\DAO\AbstractDAO;
use skyflow\Domain\AbstractModel;
use skyflow\Domain\SkyflowUser;
use Silex\Application;
/**
 * DAO class for the Skyflow user.
 */
class SkyflowUserDAO extends AbstractDAO implements UserProviderInterface
{

    /**
     * Use for the access at the security crypt/uncrypt
     * @var null|Application
     */
    protected $app = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Connection $db,
        Application $app,
        $objectType = 'users',
        $domainObjectClass = 'skyflow\\Domain\\SkyflowUser'
    ) {
        parent::__construct($db, $objectType, $domainObjectClass);
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(AbstractModel $user)
    {
        $data = parent::getData($user);
        $data['username'] = $user->getUsername();
        $data['salt'] = $user->getSalt();
        $data['password'] = $user->getPassword();
        $data['role'] = $user->getRole();
        $data['skyflowtoken'] = $this->app['skyflow.config']['security']['crypt']($user->getSkyflowtoken(),$user->getId(),$this->app);
        return $data;
    }

    /**
     * Creates a User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return SkyflowUser
     */
    protected function buildDomainObject($row)
    {
        $user = parent::buildDomainObject($row);
        $user->setUsername($row['username']);
        $user->setPassword($row['password']);
        $user->setSalt($row['salt']);
        $user->setRole($row['role']);
        $user->setSkyflowtoken($this->app['skyflow.config']['security']['uncrypt']($row['skyflowtoken'],$user->getId(),$this->app));
        return $user;
    }

    /**
     * Find a Skyflow user from its id.
     *
     * This method is different from findById() because if user is not found it
     * throws an exception. This is useful because we want to stop the application
     * right now if the skyflow user is not authenticated.
     *
     * @param  string $id  The User's id.
     * @return SkyflowUser The found User.
     * @throws \Exception  If no user is found.
     */
    public function findUserById($id)
    {
        $user = $this->findById($id);

        if ($user) {
            return $user;
        } else {
            throw new \Exception("No user matching id " . $id);
        }
    }

    /**
     * Find a Skyflow user from its skyflow-token.
     *
     * @param  string $skyflowToken The Skyflow user skyflow-token.
     * @return SkyflowUser The found Skyflow user.
     */
    public function findByToken($skyflowToken)
    {
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

        if ($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }
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
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return 'skyflow\Domain\SkyflowUser' === $class;
    }
}
