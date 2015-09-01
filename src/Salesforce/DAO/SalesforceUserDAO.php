<?php

/**
 * DAO object for the Salesforce user.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\DAO;

use Doctrine\DBAL\Connection;

use skyflow\DAO\OAuthUserDAO;
use skyflow\Domain\AbstractModel;

use Salesforce\Domain\SalesforceUser;

/**
 * DAO object for the Salesforce user.
 */
class SalesforceUserDAO extends OAuthUserDAO
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        Connection $db,
        $objectType = null,
        $domainObjectClass = 'Salesforce\\Domain\\SalesforceUser',
        $provider = 'Salesforce'
    ) {
        parent::__construct($db, $objectType, $domainObjectClass, $provider);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(AbstractModel $domainObject)
    {
        $data = parent::getData($domainObject);
        $data[$this->getProviderPrefix() . 'instance_url'] = $domainObject->getInstanceUrl();
        $data[$this->getProviderPrefix() . 'is_sandbox'] = $domainObject->getIsSandbox() ? 1 : 0;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDomainObject($row)
    {
        $domainObject = parent::buildDomainObject($row);
        $domainObject->setInstanceUrl($row[$this->getProviderPrefix() . 'instance_url']);
        $domainObject->setIsSandbox($row[$this->getProviderPrefix() . 'is_sandbox']);
        return $domainObject;
    }
}
