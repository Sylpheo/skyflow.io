<?php

namespace skyflow\Controller;

use Silex\Application;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\EntityBody;


class WaveController
{

    public function requestWaveAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user=$app['security']->getToken()->getUser();

            $waveid = $user->getWaveid();
            $wavesecret = $user->getWavesecret();
            $wavelogin = $user->getWavelogin();
            $wavepassword = $user->getWavepassword();

            /**
             * Guzzle client for wave
             */
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
            /*$data = $response->json();
            var_dump($data);*/


            $requestDatasets = $client->createRequest('GET', $responseBody->instance_url . '/services/data/v34.0/wave/datasets', [
                'headers' => ['Authorization' => 'Bearer ' . $responseBody->access_token]
            ]);

            $response = $client->send($requestDatasets);
            $body = $response->getBody();
            $data = $response->json();
           // var_dump($data['datasets']);
            $unDataset = array();
            $lesDatasets = array();
                foreach($data['datasets'] as $dataset){
                   // var_dump($dataset);echo '<br/>';echo '<br/>';echo '<br/>';echo '<br/>';
                    if(isset($dataset['currentVersionId'])){
                        $unDataset['versionId']=$dataset['currentVersionId'];
                    }
                        $unDataset['id'] = $dataset['id'];
                        $unDataset['name'] = $dataset['name'];
                        array_push($lesDatasets,$unDataset);
                }

            $form = $app['form.factory']->createBuilder('form')
                ->add('Request','textarea',array(
                    'attr' => array('cols' => '120', 'rows' => '3'),
                ))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $array = $form->getData();
                $r = $array['Request'];
                //assert($r == "q = load \"0FbB00000005KPEKA2/0FcB00000005W4tKAE\";q = filter q by 'Email' in [\"e.lodie62@hotmail.fr\"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';","Afficher $r");

                //Define wave request
                $waveRequest = $client->createRequest(
                    'POST',
                    $responseBody->instance_url . '/services/data/v34.0/wave/query',
                    [
                        'json' => [
                            'query' => $r
                            //q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";q = filter q by 'Email' in ["e.lodie62@hotmail.fr"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';

                        ]
                    ]
                );


                $waveRequest->setHeader('Content-Type', 'application/json');
                $waveRequest->setHeader('Authorization', 'Bearer ' . $responseBody->access_token);
               // var_dump($waveRequest->getBody());exit;
                $responseRequest = $client->send($waveRequest);
                $responseBody = json_decode($responseRequest->getBody());
                $data = $responseRequest->json();
                //var_dump($data);
                foreach($data['results']as $result) {
                    var_dump($result);
                }
                return $app['twig']->render('wave-apihelper.html.twig',
                    array(
                        'results'=> $data['results'],
                        'request' => $r
                    ));
            }

           return $app['twig']->render('wave-apihelper.html.twig',
                array(
                    'datasets' => $lesDatasets,
                    'requestForm' => $form->createView()
                ));

        }else{
                return $app->redirect('/login');
        }
    }
}