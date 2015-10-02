<?php

/**
 * Example flow for Salesforce simple SOQL query.
 *
 * This flow demonstrates usage of the query() method from the salesforce.data
 * service to create a simple SOQL query from a query string and get the records
 * result.
 *
 * Do NOT forget to setup the client id and client secret for the Salesforce
 * addon or this flow won't work !
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow\Example;

use Skyflow\Flow\AbstractFlow;

class SalesforceSimpleSoqlQuery extends AbstractFlow
{
    /**
     * {@inheritdoc}
     */
    public function event($requestJson)
    {
        return $this->run();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $records = $this->get('salesforce.data')->query(
            'SELECT FirstName, LastName, Account.Name '
            . 'FROM Contact '
            . 'LIMIT 5'
        );

        return $records;
    }
}
