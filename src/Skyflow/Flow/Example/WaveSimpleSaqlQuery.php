<?php

/**
 * Example flow for Wave simple SAQL query.
 *
 * This flow demonstrates usage of the query() method from the wave.data
 * service to create a simple SAQL query from a query string and get the result.
 *
 * Do NOT forget to setup the client id and client secret for the Wave addon or
 * this flow won't work !
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow\Example;

use Skyflow\Flow\AbstractFlow;

class WaveSimpleSaqlQuery extends AbstractFlow
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->event(null);
    }

    /**
     * {@inheritdoc}
     */
    public function event($requestJson)
    {
        // You have to provide the container id and version id of your dataset.
        $containerId = '0FbB000000070qNKAQ';
        $versionId = '0FcB0000000829UKAQ';

        // Send a SAQL query to Wave.
        //
        // Example response :
        // {
        //   "action": "query",
        //   "responseId": "4-9GRmYnyxBC2fVeRhrvXV",
        //   "results": {
        //     "records": [
        //       {
        //         "FirstName": "John",
        //         "LastName": "Doe"
        //       }
        //     ]
        //   },
        //   "query": "q = load \"Syflow Example TV Series Characters\";q = filter q by Email in [\"john.doe@gmail.com\"];q = foreach q generate FirstName as FirstName, LastName as LastName",
        //   "responseTime": 258
        // }

        $response = $this->get('wave.data')->query(
            'q = load "' . $containerId . '/' . $versionId . '";'
            . 'q = filter q by Email in ["john.doe@gmail.com"];'
            . 'q = foreach q generate FirstName as FirstName, LastName as LastName'
        );

        return $response;
    }
}
