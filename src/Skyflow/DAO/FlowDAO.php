<?php

/**
 * DAO class for the Flow domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Doctrine\DBAL\Connection;

use skyflow\DAO\AbstractUserOwnedDAO;
use skyflow\Domain\AbstractModel;

/**
 * DAO class for the Flow domain object.
 */
class FlowDAO extends AbstractUserOwnedDAO
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        Connection $db,
        $objectType = 'flow',
        $domainObjectClass = 'skyflow\\Domain\\Flow'
    ) {
        parent::__construct($db, $objectType, $domainObjectClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(AbstractModel $model)
    {
        $data = parent::getData($model);
        $data['name'] = $model->getName();
        $data['class'] = $model->getClass();
        $data['documentation'] = $model->getDocumentation();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $flow = parent::buildDomainObject($row);
        $flow->setName($row['name']);
        $flow->setClass($row['class']);
        $flow->setDocumentation($row['documentation']);
        return $flow;
    }

    /**
     * Find a User's Flow by name.
     *
     * @param string $name   The Flow name.
     * @param string $idUser The id of the User who owns the Flow.
     * @return Flow|null The found Flow or null if none found.
     * @throws \Doctrine\DBAL\DBALException
     * @todo Refactor this in a parent class.
     */
    public function findOne($name, $idUser)
    {
        $sql = $this->getDb()->prepare("select * from flow where name = ? and id_user = ?");
        $sql->bindValue(1, $name);
        $sql->bindValue(2, $idUser);
        $sql->execute();
        $row = $sql->fetch();

        if ($row) {
            return $this->buildDomainObject($row);
        }
    }
}
