<?php

/**
 * Abstract DAO class for DAO classes that manage models that are owned by a user.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use skyflow\DAO\AbstractDAO;
use skyflow\Domain\AbstractModel;

/**
 * Abstract DAO class for DAO classes that manage models that are owned by a user.
 *
 * This class is abstract because it does not handle a model own fields, it only
 * handle userId.
 *
 * getData(AbstractModel $domainObject) and buildDomainObject($row).
 */
abstract class AbstractUserOwnedDAO extends AbstractDAO
{
    /**
     * {@inheritdoc}
     *
     * Note that first parameter is typed AbstractModel but it must be a subclass
     * of Skyflow\Domain\AbstractUserOwnedModel in order to have the getUserId()
     * method.
     */
    public function getData(AbstractModel $model)
    {
        $data = parent::getData($model);
        $data['id_user'] = $model->getUserId();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $model = parent::buildDomainObject($row);
        $model->setUserId($row['id_user']);
        return $model;
    }

    /**
     * Find one model by name and user id.
     *
     * @param string $name The model name.
     * @param string $userId The model user id.
     * @return  AbstractModel
     * @todo Implement this. I cannot do it now because AbstractModel does not
     *       have a name. A solution has to be found.
     */
    public function findByNameUserId($name, $userId)
    {
        throw new \Exception("Not implemented");
    }

    /**
     * Find all Models owned by a User using the user's id.
     *
     * @param string $userId   The id of the User who owns the Models to find.
     * @return array An array of found Models with key: id, value: model object.
     *               Empty array if none found.
     */
    public function findAllByUserId($userId)
    {
        $sql = "select * from " . $this->getObjectType() . " where id_user =?";
        $result = $this->getDb()->fetchAll($sql, array($userId));

        $models = array();
        foreach ($result as $row) {
            $modelId = $row['id'];
            $models[$modelId] = $this->buildDomainObject($row);
        }

        return $models;
    }
}
