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
            //$email="e.lodie62@hotmail.fr";

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

            $r = "q = load \"0FbB00000005KPEKA2/0FcB00000005W4tKAE\";q = filter q by 'Email' in [\"$email\"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';";
            $data = $app['wave']->request($r,$waveid,$wavesecret,$wavelogin,$wavepassword);


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

         $triggered ='merci_wave';
           $results = $app['exacttarget']->sendTriggeredSend($clientid, $clientsecret,$triggered,$email);
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

