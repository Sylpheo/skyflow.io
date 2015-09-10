<?php

/**
 * Example flow that shows usage of the Syflow features.
 *
 * To create a flow you must create your flow code file and place it under
 * src/Skyflow/Flow. The flow file must have the same name as the flow class
 * you declared inside it.
 *
 * Then you have to declare it on the Skyflow web interface. When specifying the
 * flow class on the web interface you must type the full namespace,
 * e.g. "skyflow\Flow\ExampleFlow".
 *
 * Optionally, if you want to be able to run your flow by sending an HTTP POST
 * request you have to create an event on the Skyflow web interface and create
 * a mapping between the event and your flow ; e.g. if your you mapped an event
 * named "MyExampleFlow" to this example flow, then you can execute this flow
 * by sending an HTTP POST request to:
 * http://your-app.herokuapp.com/api/events/MyExampleFlow
 * Your HTTP POST request must have a "Skyflow-Token" header that contains your
 * Skyflow token (you can get it from the web interface).
 * Skyflow will then automatically call the required event() method of your flow.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow;

use skyflow\Flow\AbstractFlow;

/**
 * Example flow that shows usage of the Skyflow features.
 *
 * A flow must extend the class skyflow\Flow\AbstractFlow.
 */
class ExampleFlow extends AbstractFlow
{
    /**
     * The code that will be executed when receiving an HTTP POST request.
     *
     * @param string $requestJson The JSON request.
     * @return mixed The content you decide to return.
     */
    public function event($requestJson)
    {
        return $this->run();
    }

    /**
     * The code that will be executed via the "Run" button on the Skyflow web
     * interface or via Heroku Scheduler.
     *
     * This method can NOT have parameters.
     *
     * @return mixed The content you decide to return.
     */
    public function run()
    {
        $this->runSalesforce();
        $this->runWave();
    }

    /**
     * Salesforce addon usage example.
     *
     * You must setup the Salesforce addon on the Skyflow web interface before
     * running this.
     */
    public function runSalesforce()
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

    /**
     * Wave addon usage example.
     *
     * You must setup the Wave addon on the Skyflow web interface before running
     * this.
     */
    public function runWave()
    {
        // Send a SAQL query to Wave.
        //
        // Example response :
        // {
        //   "action": "query",
        //   "responseId": "4-9GRmYnyxBC2fVeRhrvXV",
        //   "results": {
        //     "records": [
        //       {
        //         "FirstName": "Elodie",
        //         "LastName": "Cantrel"
        //       }
        //     ]
        //   },
        //   "query": "q = load \"0FbB00000005KPEKA2/0FcB00000005W4tKAE\";q = filter q by Email in [\"e.lodie62@hotmail.fr\"];q = foreach q generate FirstName as FirstName, LastName as LastName",
        //   "responseTime": 258
        // }

        $response = $this->get('wave.data')->query(
            'q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";'
            . 'q = filter q by Email in ["e.lodie62@hotmail.fr"];'
            . 'q = foreach q generate FirstName as FirstName, LastName as LastName'
        );

        // Upload a dataset to Wave.
        // The dataset uploaded is a 10x10 multiplication table.
        
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
