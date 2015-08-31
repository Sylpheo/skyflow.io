<?php

/**
 * Abstract Data Access Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Doctrine\DBAL\Connection;

use skyflow\Domain\AbstractModel;

/**
 * Abstract Data Access Object class.
 */
abstract class AbstractDAO
{
    /**
     * Database connection
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * Model object type handled by this DAO.
     *
     * @var string
     */
    private $objectType;

    /**
     * Constructor
     *
     * @param Connection $db         The database connection object.
     * @param string     $objectType The model object type handled by this DAO.
     */
    public function __construct(Connection $db, $objectType = null)
    {
        $this->db = $db;
        $this->objectType = isset($objectType) ? $this->normalize($objectType) : null;
    }

    /**
     * Grants access to the database connection object.
     *
     * @return \Doctrine\DBAL\Connection The database connection object
     */
    protected function getDb()
    {
        return $this->db;
    }

    /**
     * Normalize from camelCase to snake_case for the request.
     *
     * @param  string $str The string to normalize.
     * @return string      The normalized string from camelCase to snake_case.
     * @todo Refactor this. Duplicate of :
     *       Skyflow\Authenticator\AbstractOAuthAuthenticator:normalize($str)
     */
    protected function normalize($str)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $str)), '_');
    }

    /**
     * Find a domain object by its id.
     *
     * @param $id The domain object id.
     * @return Event|null The found Event or null if none found.
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOneById($id)
    {
        $sql = $this->getDb()->prepare("select * from " . $this->objectType . " where id = ?");
        $sql->bindValue(1, $id);
        $sql->execute();
        $object = $sql->fetch();

        if ($object) {
            return $this->buildDomainObject($object);
        }
    }

    /**
     * Alias for findOneById.
     *
     * @param $id The domain object id.
     * @return Event|null The found Event or null if none found.
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findById($id)
    {
        return $this->findOneById($id);
    }

    /**
     * Get domain object data formatted for storage in database.
     *
     * It returns an associative array where the key is the
     * field name in database and the value is the current value
     * in the application.
     *
     * @param AbstractModel $domainObject The domain object to get data from.
     * @return array The domain object data formatted for storage.
     */
    abstract public function getData(AbstractModel $domainObject);

    /**
     * Save a domain object.
     *
     * @param AbstractModel $domainObject The domain object to save.
     */
    public function save(AbstractModel $domainObject)
    {
        $data = $this->getData($domainObject);

        if ($domainObject->getId()) {
            $this->getDb()->update($this->objectType, $data, array('id' => $domainObject->getIdentifier()));
        } else {
            $this->getDb()->insert($this->objectType, $data);
            $id = $this->getDb()->lastInsertId();
            $domainObject->setId($id);
        }
    }

    /**
     * Delete a domain object.
     *
     * @param string $id The id of the domain obejct to delete.
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id)
    {
        $this->getDb()->delete($this->objectType, array('id' => $id));
    }

    /**
     * Builds a domain object from a DB row.
     *
     * Must be overridden by child classes.
     *
     * @param $row The DB row ro build a Domain object from.
     */
    abstract protected function buildDomainObject($row);
}
