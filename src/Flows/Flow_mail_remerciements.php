<?php

namespace skyflow\Flows;

use Silex\Application;
use skyflow\Flows\Flow;
use Symfony\Component\HttpFoundation\Request;
use ET_Subscriber;
use ET_TriggeredSend;
use GuzzleHttp\Client;

class Flow_mail_remerciements implements Flow{

    public function event($user,$request,Application $app){
        if ($request->has('email')) {
            $email = $request->get('email');

            $clientid = $user->getClientid();
            $clientsecret = $user->getClientsecret();
            $waveid = $user->getWaveid();
            $wavesecret = $user->getWavesecret();
            $wavelogin = $user->getWavelogin();
            $wavepassword = $user->getWavepassword();

            $myclient = $app['exacttarget']->loginByApi($clientid, $clientsecret);
            $subscriber = new ET_Subscriber();
            $subscriber->authStub = $myclient;
            $subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
            $responseSub = $subscriber->get();

            $client = new Client();
            $request1 = $client->createRequest('POST', 'https://login.salesforce.com/services/oauth2/token');
            $postBody = $request1->getBody();
            $postBody->setField('client_id', $waveid);
            $postBody->setField('client_secret', $wavesecret);
            $postBody->setField('username', $wavelogin);
            $postBody->setField('password', $wavepassword);
            $postBody->setField('grant_type', 'password');
            $response = $client->send($request1);
            $responseBody = json_decode($response->getBody());

            $waveRequest = $client->createRequest(
                'POST',
                $responseBody->instance_url . '/services/data/v34.0/wave/query',
                [
                    'json' => [
                        'query' => "q = load \"0FbB00000005KPEKA2/0FcB00000005W4tKAE\";q = filter q by 'Email' in [\"$email\"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';"

                    ]
                ]
            );

            $waveRequest->setHeader('Content-Type', 'application/json');
            $waveRequest->setHeader('Authorization', 'Bearer ' . $responseBody->access_token);
            $response = $client->send($waveRequest);
            $responseBody = json_decode($response->getBody());
            $data = $response->json();

            if(isset($data['results']['records'][0])){
                $firstName = $data['results']['records'][0]['FirstName'];
                $lastName = $data['results']['records'][0]['LastName'];
            }else{
                $firstName ="";
                $lastName="";
            }

            /**
             * If subscriber does not exist
             * -> add
             */
            if (empty($responseSub->results)) {
                $subscriber = new ET_Subscriber();
                $subscriber->authStub = $myclient;
                $subscriber->props = array(
                    "EmailAddress" => $email,
                    "SubscriberKey" => $email
                );
                $subscriber->props['Attributes'] = array(array('Name' => 'FirstName', 'Value' => $firstName),
                    array('Name' => 'LastName','Value' => $lastName)
                );
                $resultsSub = $subscriber->post();

                $subKey = $email;
            } else {
                /**
                 * If subscriber already exist
                 *  -> update
                 */
                $subKey = $responseSub->results[0]->SubscriberKey;
                $subscriber = new ET_Subscriber();
                $subscriber->authStub = $myclient;
                $subscriber->props = array("SubscriberKey" => $subKey);
                $subscriber->props['Attributes'] = array(array('Name' => 'FirstName', 'Value' => $firstName),
                    array('Name' => 'LastName','Value' => $lastName)
                );

                $results = $subscriber->patch();
            }

            /**
             * Retrieve TriggeredSend
             *
             */
            $triggeredsend = new ET_TriggeredSend();
            $triggeredsend->authStub = $myclient;
            $triggeredsend->props = array('TriggeredSendStatus','Email.ID');
            $triggeredsend->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => 'merci_wave');
            $responseTrig = $triggeredsend->get();

            /**
             * Check if triggeredSendStatus is active
             *
             */
            if($responseTrig->results[0]->TriggeredSendStatus != 'Active'){
                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array("CustomerKey" => 'merci_wave', "TriggeredSendStatus"=> "Active");
                $resultsTrig = $triggeredsend->patch();
            }

            /**
             * Send triggeredSend
             *
             */
            $triggeredsend = new ET_TriggeredSend();
            $triggeredsend->authStub = $myclient;
            $triggeredsend->props = array("CustomerKey" => 'merci_wave');
            $triggeredsend->subscribers = array(array("EmailAddress"=>$email,"SubscriberKey" => $subKey));
            $results = $triggeredsend->send();

            /**
             * Check if triggerendSend status is OK
             */
            if($results->results[0]->StatusCode == 'OK'){
                return 'Message : SUCCESS ! ';
            }else{
                return 'Message : '.$results->results[0]->StatusMessage;
            }
        }
       else{
           return "Missing argument !";
       }
    }

}

