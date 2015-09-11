<?php

/**
 * DAO Object for addon queries.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\DAO;

use Doctrine\DBAL\Connection;

use Skyflow\DAO\AbstractUserOwnedDAO;
use Skyflow\Domain\AbstractModel;

/**
 * DAO Object for addons queries.
 */
class QueryDAO extends AbstractUserOwnedDAO
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        Connection $db,
        $objectType = 'query',
        $domainObjectClass = 'Skyflow\\Domain\\Query'
    ) {
        parent::__construct($db, $objectType, $domainObjectClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(AbstractModel $model)
    {
        $data = parent::getData($model);
        $data['query'] = $model->getQuery();
        $data['addon'] = $model->getAddon();
        $data['service'] = $model->getService();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $event = parent::buildDomainObject($row);
        $event->setQuery($row['query']);
        $event->setAddon($row['addon']);
        $event->setService($row['service']);
        return $event;
    }
}
