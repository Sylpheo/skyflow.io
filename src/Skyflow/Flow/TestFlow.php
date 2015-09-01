<?php

namespace Skyflow\Flow;

use skyflow\Flow\AbstractFlow;

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
        // Send a SOQL request to Salesforce.
        // Be sure the Salesforce addon is setup with client id and client secret
        // or this will not work.
        $resultSalesforce = $this->getSalesforce()->getData()->query('SELECT Name FROM Account LIMIT 1');

        // Send a SAQL request to Wave
        // Be sure the Wave addon is setup with client id and client secret or
        // this will not work.
        return $this->getWave()->getData()->query(
            'q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";'
            . 'q = filter q by Email in ["e.lodie62@hotmail.fr"];'
            . 'q = foreach q generate FirstName as FirstName, LastName as LastName'
        );
    }
}
