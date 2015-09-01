<?php

/**
 * DAO class for the Mapping domain object.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Doctrine\DBAL\Connection;

use skyflow\DAO\AbstractUserOwnedDAO;
use skyflow\DAO\EventDAO;
use skyflow\DAO\FlowDAO;
use skyflow\Domain\AbstractModel;

/**
 * DAO class for the Mapping domain object.
 */
class MappingDAO extends AbstractUserOwnedDAO
{
    /**
     * The related Event DAO object.
     *
     * The event DAO is used when binding the mapping object event using
     * mapping->setEvent().
     *
     * @var EventDAO
     */
    private $eventDAO;

    /**
     * The related Flow DAO object.
     *
     * The flow DAO is used when binding the mapping object flow using
     * mapping->setFlow().
     *
     * @var FlowDAO
     */
    private $flowDAO;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Connection $db,
        $objectType = 'mapping',
        $domainObjectClass = 'skyflow\\Domain\\Mapping'
    ) {
        parent::__construct($db, $objectType, $domainObjectClass);
    }

    /**
     * Set the related Event DAO object.
     *
     * @param EventDAO $eventDAO The related Event DAO object.
     */
    public function setEventDAO(EventDAO $eventDAO)
    {
        $this->eventDAO = $eventDAO;
    }

    /**
     * Get the related Event DAO object.
     *
     * @return EventDAO The related Event DAO object.
     */
    public function getEventDAO()
    {
        return $this->eventDAO;
    }

    /**
     * Set the related Flow DAO object.
     *
     * @param FlowDAO $flowDAO The related Flow DAO object.
     */
    public function setFlowDAO(FlowDAO $flowDAO)
    {
        $this->flowDAO = $flowDAO;
    }

    /**
     * Get the related Flow DAO object.
     *
     * @return FlowDAO The related Flow DAO object.
     */
    public function getFlowDAO()
    {
        return $this->flowDAO;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(AbstractModel $model)
    {
        $data = parent::getData($model);
        $data['id_event'] = $model->getEvent()->getId();
        $data['id_flow'] = $model->getFlow()->getId();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $mapping = parent::buildDomainObject($row);

        if (array_key_exists('id_event', $row)) {
            // Find and set the associated article
            $eventId = $row['id_event'];
            $event = $this->getEventDAO()->findOneById($eventId);
            $mapping->setEvent($event);
        }
        if (array_key_exists('id_flow', $row)) {
            $flowId = $row['id_flow'];
            $flow = $this->getFlowDAO()->findOneById($flowId);
            $mapping->setFlow($flow);
        }

        return $mapping;
    }

    /**
     * Find a Mapping from it's User id and related Event id.
     *
     * @param string $eventId The id of the related Event.
     * @param string $userId  The id of the User who owns the Mapping.
     * @return Mapping|null The found Mapping or null if none found.
     */
    public function findByEventUser($eventId, $userId)
    {
        $sql = $this->getDb()->prepare("select * from mapping where id_event = ? and id_user = ?");
        $sql->bindValue(1, $eventId);
        $sql->bindValue(2, $userId);
        $sql->execute();
        $mapping = $sql->fetch();

        if ($mapping) {
            return $mapping;
        }
    }
}
