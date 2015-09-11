<?php

/**
 * Abstract Data Access Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\DAO;

use Doctrine\DBAL\Connection;

use Skyflow\Domain\AbstractModel;

/**
 * Abstract Data Access Object class.
 *
 * This class is abstract because it only manage the id of a Model. Subclasses
 * must handle model's other fields.
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
     * The class to instantiate during buildDomainObject.
     *
     * This is the class that is instantiated during buildDomainObject().
     *
     * @var string
     */
    private $domainObjectClass;

    /**
     * Constructor
     *
     * @param Connection $db                The database connection object.
     * @param string     $objectType        The model object type handled by this
     *                                      DAO.
     * @param string     $domainObjectClass The domain object class instantiated
     *                                      by this DAO.
     *
     */
    public function __construct(Connection $db, $objectType, $domainObjectClass)
    {
        $this->db = $db;
        $this->objectType = $this->normalize($objectType);
        $this->domainObjectClass = $domainObjectClass;
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
     * Get the model obejct type handled by this DAO.
     *
     * @var string
     */
    protected function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Get the class to instantiate during buildDomainObject.
     *
     * @return string The class full name with namespace.
     */
    protected function getDomainObjectClass()
    {
        return $this->domainObjectClass;
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
     * Get domain object data formatted for storage in database.
     *
     * It returns an associative array where the key is the
     * field name in database and the value is the current value
     * in the application.
     *
     * The id MUST NOT be part of getData return array. This is because the id
     * must never be changed. As we only know the id at this point, this method
     * returns empty array to allow parent::getData($model) on subclasses.
     *
     * @param AbstractModel $model The domain object to get data from.
     * @return array The domain object data formatted for storage.
     */
    public function getData(AbstractModel $model)
    {
        return array();
    }

    /**
     * Builds a domain object from a DB row.
     *
     * At this point we only know the id. Subclasses must use
     * $model = parent::buildDomainObject($row) to add additional fields.
     *
     * @param $row The DB row ro build a Domain object from.
     */
    protected function buildDomainObject($row)
    {
        $model = new $this->domainObjectClass();
        $model->setId($row['id']);
        return $model;
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
     * Save a domain object.
     *
     * @param AbstractModel $model The domain object to save.
     */
    public function save(AbstractModel $model)
    {
        $data = $this->getData($model);

        if ($model->getId()) {
            $this->getDb()->update($this->objectType, $data, array('id' => $model->getIdentifier()));
        } else {
            $this->getDb()->insert($this->objectType, $data);
            $id = $this->getDb()->lastInsertId();
            $model->setId($id);
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
}
