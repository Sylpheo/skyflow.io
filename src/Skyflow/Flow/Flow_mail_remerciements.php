<?php

/**
 * Flow "mail remerciements".
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Flow;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use skyflow\Flow\AbstractFlow;

/**
 * Flow "mail remerciements".
 */
class Flow_mail_remerciements extends AbstractFlow {

    /**
     * This example flow event method accepts a HTTP POST request.
     *
     * The POST request must have headers :
     * Skyflow-Token: 123456789
     * Content-Type: application/json
     *
     * The Skyflow-Token is the token associated with your user. You can
     * find it from the skyflow web interface if you forgot it.
     *
     * The POST request must have JSON body :
     * {
     *     "email": "youremail@emailprovider.com"
     * }
     *
     * "emailprovider" may be yahoo, gmail, etc... The flow
     * returns an error if the email parameter is missing.
     *
     * This event method searches Wave for the FirstName and LastName associated with the email.
     * Then it searches ExactTarget for the Subscriber associated with the email.
     * It updates the ExactTarget Subscriber with FirstName and LastName found from Wave.
     * Finally it executes the TrigerredSend named "merci_wave".
     *
     * @param Request The HTTP request.
     */
    public function event($requestJson) {
        if ($requestJson->request->has('email')) {
            $app = $this->app;
            $email = $requestJson->request->get('email');

            $waverequest = "q = load \"0FbB00000005KPEKA2/0FcB00000005W4tKAE\";q = filter q by 'Email' in [\"$email\"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';";
            $data = $app['wave']->request($waverequest);

            if (isset($data['results']['records'][0])) {
                $firstName = $data['results']['records'][0]['FirstName'];
                $lastName = $data['results']['records'][0]['LastName'];
            } else {
                $firstName = "";
                $lastName = "";
            }

            $exacttarget = $this->app['exacttarget'];
            $myclient = $exacttarget->client;

            $responseSub = $exacttarget->retrieveSubscriber();

            $props = array('EmailAddress' => $email, 'SubscriberKey' => $email);
            $attributes = array('LastName' => $lastName, 'FirstName' => $firstName);
            $upsert = $exacttarget->upsertSubscriber($props, $attributes);


            $trigger = 'merci_wave';
            $results = $exacttarget->sendTriggeredSend($trigger);
            /**
             * Check if triggerendSend status is OK
             */
            if ($results->results[0]->StatusCode == 'OK') {
                return 'Message : SUCCESS ! ';
            } else {
                return 'Message : ' . $results->results[0]->StatusMessage;
            }
        } else {
            return "Missing argument !";
        }
    }
}