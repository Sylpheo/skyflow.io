<?php

namespace exactSilex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use ET_Subscriber;
use ET_TriggeredSend;
use ET_Email;

use GuzzleHttp\Client;
use CommerceGuys\Guzzle\Oauth2\GrantType\RefreshToken;
use CommerceGuys\Guzzle\Oauth2\GrantType\PasswordCredentials;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
use CommerceGuys\Guzzle\Oauth2\GrantType\ClientCredentials;
/*use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;*/


class ApiController
{

    public function eventAction($event, Request $request, Application $app)
    {
        if ($request->headers->has('Skyflow-Token')) {
            $token = $request->headers->get('Skyflow-Token');

            $user = $app['dao.user']->findByToken($token);

            if (empty($user)) {
                return $app->json('No user matching');
            }


            $clientid = $user->getClientid();
            $clientsecret = $user->getClientsecret();
            $waveid = $user->getWaveid();
            $wavesecret = $user->getWavesecret();
            $wavelogin = $user->getWavelogin();
            $wavepassword = $user->getWavepassword();

            $idUser = $user->getId();

            $unEvent = $app['dao.event']->findOne($event, $idUser);
            $trigger = $unEvent['triggersend'];

            $a = explode('_',$trigger);

            if ($request->request->has('email')) {
                $email = $request->request->get('email');

                //Retrieve subscriberkey
                $myclient = $app['exacttarget']->loginByApi($clientid, $clientsecret);
                $subscriber = new ET_Subscriber();
                $subscriber->authStub = $myclient;
                //$subscriber->props = array('EmailAddress', 'SubscriberKey');
                $subscriber->filter = array('Property' => 'EmailAddress', 'SimpleOperator' => 'equals', 'Value' => $email);
                $responseSub = $subscriber->get();
                 //var_dump($response);exit;

                if(isset($a[1]) && $a[1]== "wave") {
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
                    //$data = $response->json();
                    //var_dump($data);

                    $waveRequest = $client->createRequest(
                        'POST',
                        $responseBody->instance_url . '/services/data/v34.0/wave/query',
                        [
                            'json' => [
                                'query' => "q = load \"0FbB00000005D7wKAE/0FcB00000005SD3KAM\"; q = filter q by 'FirstName' in [\"Pierre\"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';"
                            ]
                        ]
                    );
                    $waveRequest->setHeader('Content-Type', 'application/json');
                    $waveRequest->setHeader('Authorization', 'Bearer ' . $responseBody->access_token);
                    $response = $client->send($waveRequest);
                    $responseBody = json_decode($response->getBody());
                    $data = $response->json();

                    $firstName = $data['results']['records'][0]['FirstName'];
                    $lastName = $data['results']['records'][0]['LastName'];

                    //Si il n'existe pas on le crée + infos wave
                    if (empty($responseSub->results)) {
                        $subscriber = new ET_Subscriber();
                        $subscriber->authStub = $myclient;
                        $subscriber->props = array(
                            "EmailAddress" => $email,
                            "SubscriberKey" => $email
                        );
                        $subscriber->props['Attributes'] = array(array('Name' => 'FirstName', 'Value' => $firstName));
                        $resultsSub = $subscriber->post();
                        //var_dump($resultsSub);exit;

                        $subKey = $email;
                    } else {
                        //On ajoute les infos wave
                        $subKey = $responseSub->results[0]->SubscriberKey;
                        $subscriber = new ET_Subscriber();
                        $subscriber->authStub = $myclient;
                        $subscriber->props = array("SubscriberKey" => $subKey);
                        $subscriber->props['Attributes'] = array(array('Name' => 'FirstName', 'Value' => $firstName));
                        $subscriber->props['Attributes'] = array(array(
                          'Name' => 'LastName', 'Value' => $lastName));
                        $results = $subscriber->patch();
                        // var_dump($results);exit;
                    }
                }

                //Si il n'existe pas on le crée
                if (empty($responseSub->results)) {
                    $subscriber = new ET_Subscriber();
                    $subscriber->authStub = $myclient;
                    $subscriber->props = array(
                        "EmailAddress" => $email,
                        "SubscriberKey" => $email
                    );
                    $resultsSub = $subscriber->post();
                    //var_dump($resultsSub);exit;

                    $subKey = $email;
                } else {
                    $subKey = $responseSub->results[0]->SubscriberKey;
                    // var_dump($results);exit;

                }

                //Retrieve TriggeredSend
                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array('TriggeredSendStatus','Email.ID');
                $triggeredsend->filter = array('Property' => 'CustomerKey','SimpleOperator' => 'equals','Value' => $trigger);
                $responseTrig = $triggeredsend->get();

                //var_dump($responseTrig);

                if($responseTrig->results[0]->TriggeredSendStatus != 'Active'){
                    //Set triggeredSendStatus -> Active
                    $triggeredsend = new ET_TriggeredSend();
                    $triggeredsend->authStub = $myclient;
                    $triggeredsend->props = array("CustomerKey" => $trigger, "TriggeredSendStatus"=> "Active");
                    $resultsTrig = $triggeredsend->patch();

                }

                //Send !
                $triggeredsend = new ET_TriggeredSend();
                $triggeredsend->authStub = $myclient;
                $triggeredsend->props = array("CustomerKey" => $trigger);
                $triggeredsend->subscribers = array(array("EmailAddress"=>$email,"SubscriberKey" => $subKey));
                $results = $triggeredsend->send();
                var_dump($results);exit;

                if($results->results[0]->StatusCode == 'OK'){
                    return $app->json('Message : SUCCESS ! ');
                }else{
                    return $app->json('Message : '.$results->results[0]->StatusMessage);
                }


            } else {
                return $app->json('Missing argument ! ', 400);
            }
        } else {
            return $app->json('Missing Skyflow Token ! ');
        }

    }

    public function waveAction(Application $app)
    {

        $access_token = $app['session']->get('access_token');
        $instance_url = $app['session']->get('instance_url');

        $client = new Client();

        $request = $client->createRequest('GET', $instance_url . '/services/data/v34.0/wave', [
            'headers' => ['Authorization' => 'Bearer ' . $access_token]
        ]);

        $response = $client->send($request);
        $body = $response->getBody();

        return $body;
    }

    public function testAction(Application $app)
    {
        $query = array("query" => "q = load \"0FbB00000005D7wKAE/0FcB00000005SD3KAM\";
          q = filter q by 'FirstName' in [\"Pierre\"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';");

        $data = json_encode($query);
        $access_token = $app['session']->get('access_token');
        $instance_url = $app['session']->get('instance_url');

        $curl2 = curl_init($instance_url . '/services/data/v34.0/wave/query');
        curl_setopt($curl2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ));
        curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl2, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);

        $rep = curl_exec($curl2);
        curl_close($curl2);
        // echo $rep;
        $a = explode('records', $rep);
//var_dump($a[1]);
        $b = explode('query', $a[1]);
//var_dump($b[0]);
        //parse_str($rep,$arr);

    }
}