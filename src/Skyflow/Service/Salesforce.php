<?php
namespace skyflow\Service;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;


class Salesforce {

/*    public $app;

    public $client;

    public $instance_url;

    public $access_token;

    public $refresh_token;

    public function __construct($app, $params){
        $this->app = $app;
        $instance_url = $params['instance_url'];
        $access_token = $params['access_token'];
        $refresh_token = $params['refresh_token'];

        if($instance_url == null || $access_token == null || $refresh_token == null){
            throw new Exception('instance_url, refresh_token or access_token is null in params');
        }
        $this->instance_url = $instance_url;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
    }
*/
    public static function login(Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
            $client_id = $user->getWaveId();

            if ($user->getSalesforcesandbox()) {
                $login_URI = "https://test.salesforce.com";
            } else {
                $login_URI = "https://login.salesforce.com";
            }

            $redirect_URI = 'https://' . $_SERVER['HTTP_HOST'] . '/auth/salesforce/oauth2callback';
            $auth_url = $login_URI . "/services/oauth2/authorize?response_type=code&client_id="
                . $client_id . "&redirect_uri=" . urlencode($redirect_URI);

            $client = new Client();
            $reponse = $client->get($auth_url);
            return $reponse->getBody();
        }
    }

    public static function loginByAPI(Application $app)
    {
        if ($app['request']->headers->has('Skyflow-Token')) {
            $token = $app['request']->headers->get('Skyflow-Token');

            $user = $app['dao.user']->findByToken($token);

            if (empty($user)) {
                return $app->json('No user matching');
            }

            $client_id = $user->getWaveId();

            if ($user->getSalesforcesandbox()) {
                $login_URI = "https://test.salesforce.com";
            } else {
                $login_URI = "https://login.salesforce.com";
            }

            $redirect_URI = 'https://' . $_SERVER['HTTP_HOST'] . '/auth/salesforce/oauth2callback';
            $auth_url = $login_URI . "/services/oauth2/authorize?response_type=code&client_id="
                . $client_id . "&redirect_uri=" . urlencode($redirect_URI);

            $client = new Client();
            $reponse = $client->get($auth_url);
            return $reponse->getBody();
        }else{
            throw new Exception('Missing Skyflow-Token');
        }
    }

    /***
     * Get access_token, refresh_token, instance_url
     * @param Application $app
     * @param $code
     * @return Guzzle response
     */
    public static function callback(Application $app,$code){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();

            $client_id = $user->getSalesforceId();
            $client_secret = $user->getSalesforceSecret();

            if ($user->getSalesforcesandbox()) {
                $login_URI = "https://test.salesforce.com";
            } else {
                $login_URI = "https://login.salesforce.com";
            }

            $redirect_URI = 'https://' . $_SERVER['HTTP_HOST'] . '/auth/salesforce/oauth2callback';
            $token_url = $login_URI . "/services/oauth2/token";

            if (!isset($code) || $code == "") {
                die("Error");
            }

            $client = new Client();
            $request = $client->createRequest('POST', $token_url);
            $postBody = $request->getBody();
            $postBody->setField('code', $code);
            $postBody->setField('client_id', $client_id);
            $postBody->setField('client_secret', $client_secret);
            $postBody->setField('redirect_uri', $redirect_URI);
            $postBody->setField('grant_type', 'authorization_code');
            $response = $client->send($request);
            $responseBody = json_decode($response->getBody());

            return $responseBody;
        }
    }

    /**
     * Get access_token, instance_url, refresh_token (by API)
     * @param Application $app
     * @return Guzzle JsonResponse
     * @throws Exception
     */
    public static function callbackByApi(Application $app){
        if ($app['request']->headers->has('Skyflow-Token')) {
            $token = $app['request']->headers->get('Skyflow-Token');

            $user = $app['dao.user']->findByToken($token);

            if (empty($user)) {
                return $app->json('No user matching');
            }
            $client_id = $user->getSalesforceId();
            $client_secret = $user->getSalesforceSecret();

            if ($user->getSalesforcesandbox()) {
                $login_URI = "https://test.salesforce.com";
            } else {
                $login_URI = "https://login.salesforce.com";
            }

            $redirect_URI = 'https://' . $_SERVER['HTTP_HOST'] . '/auth/salesforce/oauth2callback';
            $token_url = $login_URI . "/services/oauth2/token";

            if (!isset($code) || $code == "") {
                die("Error");
            }

            $client = new Client();
            $request = $client->createRequest('POST', $token_url);
            $postBody = $request->getBody();
            $postBody->setField('code', $code);
            $postBody->setField('client_id', $client_id);
            $postBody->setField('client_secret', $client_secret);
            $postBody->setField('redirect_uri', $redirect_URI);
            $postBody->setField('grant_type', 'authorization_code');
            $response = $client->send($request);
            $responseBody = json_decode($response->getBody());

            return $responseBody;
        }else{
            throw new Exception('Missing Skyflow-Token');
        }
    }

    /***
     * @param Application $app
     * @return new access_token
     */
    public static function refreshToken(Application $app){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
            $client_id = $user->getSalesforceId();
            $client_secret = $user->getSalesforceSecret();
            $refresh_token = $user->getRefreshTokenSalesforce();

            if ($user->getSalesforcesandbox()) {
                $token_url = "https://test.salesforce.com/services/oauth2/token";
            } else {
                $token_url = "https://login.salesforce.com/services/oauth2/token";
            }

            $client = new Client();
            $request = $client->createRequest('POST', $token_url);
            $postBody = $request->getBody();
            $postBody->setField('grant_type', "refresh_token");
            $postBody->setField('client_id', $client_id);
            $postBody->setField('client_secret', $client_secret);
            $postBody->setField('refresh_token', $refresh_token);

            $response = $client->send($request);
            $responseBody = json_decode($response->getBody());

            return $responseBody;
        }
    }

    /**
     * Get new access_token (API)
     * @param Application $app
     * @return mixed|\Symfony\Component\HttpFoundation\JsonResponse
     * @throws Exception
     */
    public static function refreshTokenByApi(Application $app)
    {
        if ($app['request']->headers->has('Skyflow-Token')) {
            $token = $app['request']->headers->get('Skyflow-Token');

            $user = $app['dao.user']->findByToken($token);

            if (empty($user)) {
                return $app->json('No user matching');
            }

            $client_id = $user->getSalesforceId();
            $client_secret = $user->getSalesforceSecret();
            $refresh_token = $user->getRefreshTokenSalesforce();

            if ($user->getSalesforcesandbox()) {
                $token_url = "https://test.salesforce.com/services/oauth2/token";
            } else {
                $token_url = "https://login.salesforce.com/services/oauth2/token";
            }

            $client = new Client();
            $request = $client->createRequest('POST', $token_url);
            $postBody = $request->getBody();
            $postBody->setField('grant_type', "refresh_token");
            $postBody->setField('client_id', $client_id);
            $postBody->setField('client_secret', $client_secret);
            $postBody->setField('refresh_token', $refresh_token);

            $response = $client->send($request);
            $responseBody = json_decode($response->getBody());

            return $responseBody;

        }else{
            throw new Exception('Missing Skyflow-Token');
        }
    }

    /**
     * Send request to Saleforce
     * @param Application $app
     * @param $salesforceRequest
     * @return mixed|string
     */
    public static function request(Application $app,$salesforceRequest){
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['security']->getToken()->getUser();
            $access_token = $user->getAccessTokenSalesforce();
            $instance_url = $user->getInstanceUrlSalesforce();

            $client = new Client();
            $salesforceRequest = $client->createRequest(
                'GET',
                $instance_url . "/services/data/v20.0/query?q=" . urlencode($salesforceRequest)
            );

            $salesforceRequest->setHeader('Authorization', 'OAuth ' . $access_token);

            $response = $client->send($salesforceRequest);
            $responseBody = json_decode($response->getBody());
            $data = $response->json();
            $data = json_encode($data);

            return $data;
        }
    }
}

