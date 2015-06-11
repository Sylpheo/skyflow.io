<?php

namespace exactSilex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use ET_Subscriber;
use ET_TriggeredSend;

use FacebookStrategy;


class ApiController {

    public function eventAction($event,Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $idUser= $app['security']->getToken()->getUser()->getId();
            $unEvent = $app['dao.event']->findOne($event,$idUser);
            $trigger=$unEvent['triggerSend'];

             if($request->request->has('email')){
                $email = $request->request->get('email');

                //Retrieve subscriberkey
                $myclient = $app['exacttarget']->login($app);
                $subscriber = new ET_Subscriber();
                $subscriber->authStub = $myclient;
                $subscriber->props=array('EmailAddress','SubscriberKey');
                $subscriber->filter=array('Property'=>'EmailAddress','SimpleOperator'=>'equals','Value'=>$email);
                $response = $subscriber->get();
                $subKey = $response->results[0]->SubscriberKey;

                //Retrieve TriggeredSend
                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array('TriggeredSendStatus');
                $triggeredsend->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $trigger);
                $responseTrig = $triggeredsend->get();

                    if($responseTrig->results[0]->TriggeredSendStatus != 'Active'){
                        //Set triggeredSendStatus -> Active
                        $triggeredsend = new ET_TriggeredSend();
                        $triggeredsend->authStub = $myclient;
                        $triggeredsend->props = array("CustomerKey" => $trigger, "TriggeredSendStatus"=> "Active");
                        $results = $triggeredsend->patch();
                    }

                //Send !
                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array("CustomerKey" => $trigger);
                $triggeredsend->subscribers = array(array("EmailAddress"=>$email,"SubscriberKey" => $subKey));
                $results = $triggeredsend->send();


                    if($results->results[0]->StatusCode == 'OK'){
                        return $app->json('Message : SUCCESS ! ');
                    }else{
                            return $app->json('Message : Error ! ');
                    }

             }else{
                    return $app->json('Missing argument ! ',400);
             }
        }else{
                return $app->json('Not connected !');
        }
    }

    public function waveAction(Application $app){
        /*$loginurl = "https://gs0.salesforce.com/services/oauth2/token";
        $client_id= "3MVG9SemV5D80oBcbOkdI2WCxIIA5fZMPI3ZDTZBBU_6E6zc8Z5wKZ4DCh.bPDxBEV4PocUnC3ELl70tjOSof";
        $client_secret="8180025755972035170";
        $username= "pierre.lecointre@sylpheo.dev";
        $password= "easy1234NWu2pdAhMFXl6KyyHPu5YDhy";

        $params = $app['wave']->login($client_id,$client_secret,$username,$password);
    
        //Récupérer instance et access token
        $curl = curl_init($loginurl);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ( $status != 200 ) {
                die("Error: call to URL failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
            }

        curl_close($curl);

       // echo $json_response;

        $response = json_decode($json_response, true);

        $access_token = $response['access_token'];

        $instance_url = $response['instance_url'];

        echo $access_token;
        echo $instance_url;

    
           if (!isset($access_token) || $access_token == "") {
                die("Error - access token missing from response!");
            }

            
           if (!isset($instance_url) || $instance_url == "") {
                die("Error - instance URL missing from response!");
            }

        $headers =array();
        $headers[]='Authorization: Bearer '.$access_token;
        $curl2 =curl_init($instance_url.'/services/data/v34.0/wave');
        curl_setopt($curl2, CURLOPT_HTTPHEADER, $headers);
        $rep = curl_exec($curl2);

            return $app->json($rep);*/


        
    
    }

    public function OpauthAction(Application $app){
         // Listen for events
        $app->on(OpauthExtension::EVENT_ERROR, function($e) {
            $this->log->error('Auth error: ' . $e['message'], ['response' => $e->getSubject()]);
            $e->setArgument('result', $this->redirect('/'));
        });

        $app->on(OpauthExtension::EVENT_SUCCESS, function($e) {
            $response = $e->getSubject();

            $app['access_token'] = $response['auth']['raw']['access_token'];
            $app['instance_url'] = $response['auth']['raw']['instance_url'];

            /*
               find/create a user, oauth response is in $response and it's already validated!
               store the user in the session
            */
            $e->setArgument('result', $app->redirect('/'));

        });
    }

  


 }