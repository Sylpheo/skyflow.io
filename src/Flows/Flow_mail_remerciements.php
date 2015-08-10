<?php

namespace skyflow\Flows;

use Silex\Application;
use skyflow\Flows\Flow;
use Symfony\Component\HttpFoundation\Request;
use ET_Subscriber;
use ET_TriggeredSend;
use GuzzleHttp\Client;

class Flow_mail_remerciements implements Flow{

    public $app;
    public function __construct($app){
        $this->app = $app;
    }

    public function event($requestJson)
    {

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

