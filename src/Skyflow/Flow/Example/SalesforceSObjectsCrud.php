<?php

/**
 * Example flow for Salesforce SObjects CRUD operations.
 *
 * This flow demonstrates usage of the methods from the salesforce.data.sobjects
 * service to create, update and delete SObjects.
 *
 * Do NOT forget to setup the client id and client secret for the Salesforce
 * addon or this flow won't work !
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow\Example;

use Skyflow\Flow\AbstractFlow;

class SalesforceSObjectsCrud extends AbstractFlow
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
        // Send a SOQL query to Salesforce.
        // The return value is an array() of found records.
        $records = $this->get('salesforce.data')->query(
            "SELECT Id FROM Account WHERE Name = 'Skyflow_ExampleFlow'"
        );

        if (count($records) === 0) {
            // Create a SObject in Salesforce.
            // The return value is the id of created sObject.
            $id = $this->get('salesforce.data.sobjects')->create('Account', array(
                'Name' => 'Skyflow_ExampleFlow'
            ));
        } else {
            $id = $records[0]['Id'];
        }

        // Update an SObject in Salesforce.
        // Returns null.
        $this->get('salesforce.data.sobjects')->update('Account', $id, array(
            'Name' => 'Skyflow_ExampleFlow_updated'
        ));

        // Delete an SObject in Salesforce.
        // Returns null.
        $this->get('salesforce.data.sobjects')->delete('Account', $id);
    }
}
