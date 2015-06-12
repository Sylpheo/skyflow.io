<?php

namespace exactSilex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use ET_Subscriber;
use ET_TriggeredSend;

/*use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;*/


class ApiController {

    public function eventAction($event,Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $idUser= $app['security']->getToken()->getUser()->getId();
            $unEvent = $app['dao.event']->findOne($event,$idUser);
            $trigger=$unEvent['triggerSend'];

            return $app->json($trigger);

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

                return $app->json($results);

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

        $access_token = $app['session']->get('access_token');
        $instance_url = $app['session']->get('instance_url');
      


       
        $headers =array();
        $headers[]='Authorization: Bearer '.$access_token;
        $curl2 =curl_init($instance_url.'/services/data/v34.0/wave/datasets');
        curl_setopt($curl2, CURLOPT_HTTPHEADER, $headers);
        $rep = curl_exec($curl2);

           return $app->json($rep);

           /* foreach($rep as $r){
                echo $r;
            }*/



        
    
    }



  


 }