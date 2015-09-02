<?php

/**
 * DAO class for the Event domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Doctrine\DBAL\Connection;

use skyflow\DAO\AbstractUserOwnedDAO;
use skyflow\Domain\AbstractModel;

/**
 * DAO class for the Event domain object.
 */
class EventDAO extends AbstractUserOwnedDAO
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        Connection $db,
        $objectType = 'event',
        $domainObjectClass = 'skyflow\\Domain\\Event'
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
        $data['description'] = $model->getDescription();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $event = parent::buildDomainObject($row);
        $event->setName($row['name']);
        $event->setDescription($row['description']);
        return $event;
    }

    /**
     * Find a User's Event by name.
     *
     * @param string $name   The Event name.
     * @param string $userId The id of the User who owns the Event.
     * @return Event|null The found Event or null if none found.
     * @throws \Doctrine\DBAL\DBALException
     * @todo Refactor this in a parent class.
     */
    public function findOne($name, $userId)
    {
        $sql = $this->getDb()->prepare("select * from event where name = ? and id_user = ?");
        $sql->bindValue(1, $name);
        $sql->bindValue(2, $userId);
        $sql->execute();
        $row = $sql->fetch();

        if ($row) {
            return $this->buildDomainObject($row);
        }
    }
}
