<?php

namespace Skyflow\Flow;

use Skyflow\Flow\AbstractFlow;

/**
 * My first flow class.
 */
class TestFlow extends AbstractFlow
{
    /**
     * The code that will be executed when a HTTP POST request is sent to
     * https://your-app-name.heroku.com/api/event/MyFirstFlow
     *
     * @param $requestJson The JSON request.
     */
    public function event($requestJson)
    {
        return $this->run();
    }

    public function run()
    {
        //return $this->runSalesforce();
        $this->runWave();
    }

    public function runSalesforce()
    {
        // Send a SOQL query to Salesforce.
        $records = $this->get('salesforce.data')->query(
            "SELECT Id FROM Account WHERE Name = 'Test_TestFlow'"
        );

        if (count($records) === 0) {
            // Create a SObject in Salesforce.
            $id = $this->get('salesforce.data.sobjects')->create('Account', array(
                'Name' => 'Test_TestFlow'
            ));
        } else {
            $id = $records[0]['Id'];
        }

        // Update an SObject in Salesforce.
        $this->get('salesforce.data.sobjects')->update('Account', $id, array(
            'Name' => 'Test_TestFlow_updated'
        ));

        // Delete an SObject in Salesforce.
        $this->get('salesforce.data.sobjects')->delete('Account', $id);
    }

    public function runWave()
    {
        $this->get('wave.data')->query(
            'q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";'
            . 'q = filter q by Email in ["e.lodie62@hotmail.fr"];'
            . 'q = foreach q generate FirstName as FirstName, LastName as LastName'
        );

        $dataset = $this->get('wave.externaldata')->create(
            'Multiplication',
            array(
                'edgemartAlias' => 'Multiplication',
                'format' => 'Csv',
                'operation' => 'Overwrite',
                'notificationSent' => 'Warnings',
                'notificationEmail' => 'adrien.desfourneaux@sylpheo.com',
                'description' => 'Multiplication table'
            )
        );

        // 1 row for column headers
        $dataset->appendCsvLine(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));

        // 10 rows multiplication table.
        for ($i = 1; $i <= 10; $i++) {
            $row = array();

            // 10 columns multiplication table
            for ($j = 1; $j <= 10; $j++) {
                $row[$j] = $i * $j;
            }

            $dataset->appendCsvLine($row);
        }

        $dataset->process();
    }
}
