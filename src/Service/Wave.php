<?php
namespace skyflow\Service;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;


class Wave {

    public $app;

    /**
     * Guzzle Client
     */
    public $client;

    public $instance_url;

    public $access_token;

    public function __construct($app, $params){
        $this->app = $app;
        $instance_url = $params['instance_url'];
        $access_token = $params['access_token'];

        if($instance_url === null || $access_token === null){
            throw new Exception('instance_url or access_token is null in params');
        }
        $this->instance_url = $instance_url;
        $this->access_token = $access_token;
    }

	public static function login($app){

        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $waveid = $app['security']->getToken()->getUser()->getWaveid();
            $wavesecret = $app['security']->getToken()->getUser()->getWavesecret();
            $wavelogin = $app['security']->getToken()->getUser()->getWavelogin();
            $wavepassword = $app['security']->getToken()->getUser()->getWavepassword();

            if($waveid === null || $wavesecret === null || $wavelogin === null || $wavepassword === null){
                throw new Exception('waveid, wavesecret, wavepassword or wavelogin is null : must be provided in database before using application');
            }

            $client = new Client();
            $request = $client->createRequest('POST', 'https://login.salesforce.com/services/oauth2/token');
            $postBody = $request->getBody();
            $postBody->setField('client_id', $waveid);
            $postBody->setField('client_secret', $wavesecret);
            $postBody->setField('username', $wavelogin);
            $postBody->setField('password', $wavepassword);
            $postBody->setField('grant_type', 'password');
            $response = $client->send($request);
            $responseBody = json_decode($response->getBody());

            $wave = new Wave($app, array(
                'instance_url' => $responseBody->instance_url,
                'access_token' => $responseBody->access_token
            ));
            $wave->client = $client;

            return $wave;
        }else{
            throw new Exception('User is not fully authenticated');
        }
	}

    public static function loginByApi($app){
        if ($app['request']->headers->has('Skyflow-Token')) {
            $token = $app['request']->headers->get('Skyflow-Token');

            $user = $app['dao.user']->findByToken($token);

            if (empty($user)) {
                return $app->json('No user matching');
            }

            $waveid = $user->getWaveid();
            $wavesecret = $user->getWavesecret();
            $wavelogin = $user->getWavelogin();
            $wavepassword = $user->getWavepassword();

            if($waveid === null || $wavesecret === null || $wavelogin === null || $wavepassword === null){
                throw new Exception('waveid, wavesecret, wavepassword or wavelogin is null : must be provided in database before using application');
            }

            $client = new Client();
            $request = $client->createRequest('POST', 'https://login.salesforce.com/services/oauth2/token');
            $postBody = $request->getBody();
            $postBody->setField('client_id', $waveid);
            $postBody->setField('client_secret', $wavesecret);
            $postBody->setField('username', $wavelogin);
            $postBody->setField('password', $wavepassword);
            $postBody->setField('grant_type', 'password');
            $response = $client->send($request);
            $responseBody = json_decode($response->getBody());

            $wave = new Wave($app, array(
                'instance_url' => $responseBody->instance_url,
                'access_token' => $responseBody->access_token
            ));
            $wave->client = $client;

            return $wave;

        }else{
            throw new Exception('Missing Skyflow-Token');
        }
    }

    public function request($request){
        $waveRequest = $this->client->createRequest(
            'POST',
            $this->instance_url . '/services/data/v34.0/wave/query',
            [
                'json' => [
                    'query' => $request
                ]
            ]
        );

        $waveRequest->setHeader('Content-Type', 'application/json');
        $waveRequest->setHeader('Authorization', 'Bearer ' . $this->access_token);
        $response = $this->client->send($waveRequest);
        $responseBody = json_decode($response->getBody());
        $data = $response->json();

        return $data;
    }
 }

