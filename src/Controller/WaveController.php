<?php

namespace skyflow\Controller;

use Silex\Application;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\EntityBody;
use skyflow\Domain\Wave_request;


class WaveController
{

    public function requestWaveAction(Request $request,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user=$app['security']->getToken()->getUser();

            $id_user = $user->getId();
            $waveid = $user->getWaveid();
            $wavesecret = $user->getWavesecret();
            $wavelogin = $user->getWavelogin();
            $wavepassword = $user->getWavepassword();

            $history = $app['dao.wave_request']->findAllByUser($id_user);

            $form = $app['form.factory']->createBuilder('form')
                ->add('Request','textarea',array(
                    'attr' => array('cols' => '120', 'rows' => '3'),
                ))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $array = $form->getData();
                $r = $array['Request'];

                $result = $app['dao.wave_request']->findByRequest($r,$id_user);
                if($result == null){
                    $r_wave = new Wave_request();
                    $r_wave->setIdUser($id_user);
                    $r_wave->setRequest($r);
                    $app['dao.wave_request']->save($r_wave);
                }

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
                $responseRequest = $client->send($waveRequest);
                $responseBody = json_decode($responseRequest->getBody());
                $data = $responseRequest->json();
                $data = json_encode($data);
               // var_dump($data);exit;
            }

           return $app['twig']->render('wave-apihelper.html.twig',
                array(
                    'requestForm' => $form->createView(),
                    'history' => $history,
                ));

        }else{
                return $app->redirect('/login');
        }
    }

    public function resendAction($id,Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $wave_request = $app['dao.wave_request']->findById($id);

            $user = $app['security']->getToken()->getUser();

            $id_user = $user->getId();
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
            //Define wave request
            $waveRequest = $client->createRequest(
                'POST',
                $responseBody->instance_url . '/services/data/v34.0/wave/query',
                [
                    'json' => [
                        'query' => $wave_request->getRequest()
                        //q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";q = filter q by 'Email' in ["e.lodie62@hotmail.fr"];q = foreach q generate 'FirstName' as 'FirstName','LastName' as 'LastName';

                    ]
                ]
            );

            $waveRequest->setHeader('Content-Type', 'application/json');
            $waveRequest->setHeader('Authorization', 'Bearer ' . $responseBody->access_token);
            $responseRequest = $client->send($waveRequest);
            $responseBody = json_decode($responseRequest->getBody());
            $data = $responseRequest->json();
            $data = json_encode($data);
            var_dump($data);
        }
    }
}