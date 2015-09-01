<?php

/**
 * DAO Object for addon queries.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\DAO;

use Doctrine\DBAL\Connection;

use skyflow\DAO\AbstractUserOwnedDAO;
use skyflow\Domain\AbstractModel;

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
        $domainObjectClass = 'skyflow\\Domain\\Query'
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
