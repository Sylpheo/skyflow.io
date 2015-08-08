<?php
namespace skyflow\Service;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;


class Wave {

	public static function login($waveid,$wavesecret,$wavelogin,$wavepassword){

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

        return $responseBody;
	}


    public function request($request,$waveid,$wavesecret,$wavelogin,$wavepassword){
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
                    'query' => $request

                ]
            ]
        );

        $waveRequest->setHeader('Content-Type', 'application/json');
        $waveRequest->setHeader('Authorization', 'Bearer ' . $responseBody->access_token);
        $response = $client->send($waveRequest);
        $responseBody = json_decode($response->getBody());
        $data = $response->json();
        return $data;
    }


 }

