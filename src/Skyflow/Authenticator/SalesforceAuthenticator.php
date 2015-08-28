<?php

/**
 * Saleforce OAuth2 authenticator.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Authenticator;

use GuzzleHttp\Client;

use skyflow\Authenticator\AuthenticatorInterface;

/**
 * Salesforce OAuth2 authenticator.
 *
 * Usage:
 *
 * ```php
 * // username-password flow
 * $authenticator = new SalesforceAuthenticator(array(
 *     'login_url'     => 'https://login.salesforce.com/',
 *     'grant_type'    => 'password',
 *     'client_id'     => 'your_application_client_id',
 *     'client_secret' => 'your_application_client_secret',
 *     'username'      => 'your_username',
 *     'password'      => 'your_password'
 * ));
 *
 * $authenticator->authenticate();
 * echo "instance_url is " . $authenticator->instance_url;
 * echo "access_token is " . $authenticator->access_token;
 * echo "refresh_token is " . $authenticator->refresh_token;
 * ```
 *
 * ```php
 * // Authentication code flow
 * $authenticator = new SalesforceAuthenticator(array(
 *     'login_url'     => 'https://test.salesforce.com/',
 *     'response_type' => 'code',
 *     'client_id'     => 'your_application_client_id',
 *     'redirect_uri'  => 'https://mysuperapplication.herokuapp.com/auth/saleforce/oauth2callback'
 * ));
 *
 * // Exit the application and redirect to Salesforce authorize url
 * $authenticator->authenticate();
 * // or use $authenticator->authenticate(false);
 * // if you want to exit the application later.
 *
 * // Here the user is logging in to Salesforce.
 *
 * // Later in the callback controller action ($code is the authorization code):
 * $authenticator = new SalesforceAuthenticator(array(
 *     'login_url'     => 'https://test.salesforce.com/',
 *     'code'          => $code,
 *     'client_id'     => 'your_application_client_id',
 *     'client_secret' => 'your_application_client_secret',
 *     'redirect_uri'  => 'https://yoursuperapplication.herokuapp.com/auth/saleforce/oauth2callback'
 * ));
 *
 * $authenticator->callback();
 * echo "instance_url is " . $authenticator->instance_url;
 * echo "access_token is " . $authenticator->access_token;
 * echo "refresh_token is " . $authenticator->refresh_token;
 * ```
 *
 * ```php
 * // Refresh token flow
 * $authenticator = new SalesforceAuthenticator(array(
 *     'login_url'     => 'https://test.salesforce.com/',
 *     'refresh_token' => $refresh_token,
 *     'client_id'     => 'your_application_client_id',
 *     'client_secret' => 'your_application_client_secret'
 * ));
 *
 * $authenticator->refresh();
 * echo "access_token is " . $authenticator->access_token;
 */
class SalesforceAuthenticator implements AuthenticatorInterface
{
    /**
     * Salesforce login URL.
     *
     * For example: "https://login.salesforce.com/" or "https://test.salesforce.com/".
     * May or may not contain trailing slash.
     *
     * @var string
     */
    public $loginUrl;

    /**
     * Response type.
     *
     * For example: "code".
     *
     * @var string
     */
    public $responseType;

    /**
     * Grant type.
     *
     * For example: "code" or "password".
     *
     * @var string
     */
    public $grantType;

    /**
     * Connected application client id.
     *
     * Can be found from Salesforce setup interface
     * in Setup -> Create -> Apps -> "Your app" -> Consumer Key.
     *
     * @var string
     */
    public $clientId;

    /**
     * Connected application client secret.
     *
     * Can be found from Salesforce setup interface
     * in Setup -> Create -> Apps -> "Your app" -> Consumer Secret.
     *
     * @var string
     */
    public $clientSecret;

    /**
     * Connected application redirect uri.
     *
     * @var string
     */
    public $redirectUri;

    /**
     * Salesforce user username for username-password flow.
     *
     * @var string
     */
    public $username;

    /**
     * Salesforce user password for username-password flow.
     *
     * Append the security token if you have one.
     *
     * @var string
     */
    public $password;

    /**
     * Salesforce authentication code.
     *
     * @var string
     */
    public $code;

    /**
     * Salesforce instance url.
     *
     * @var string
     */
    public $instanceUrl;

    /**
     * Salesforce Access token.
     *
     * @var string
     */
    public $accessToken;

    /**
     * Salesforce Refresh token.
     *
     * @var string
     */
    public $refreshToken;

    /**
     * SalesforceAuthenticator constructor.
     *
     * @param array $parameters An associative array of URL parameters/response in snake_case format. For example: "grant_type", "client_id"...
     */
    public function __construct($parameters = null)
    {
        if (is_array($parameters)) { 
            foreach ($parameters as $key => $value) {
                $property = $this->denormalize($key);
                
                if (property_exists($this, $property)) {
                    $this->$property = $value;
                }
            }
        }
    }

    /**
     * Normalize from camelCase to snake_case for the request.
     *
     * @param string $str The string to normalize.
     * @return string The normalized string from camelCase to snake_case.
     */
    protected function normalize($str)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $str)), '_');
    }

    /**
     * Denormalize from snake_case to camelCase for storage in attributes.
     *
     * @param string $str The string to denormalize.
     * @return string The denormalized string from snake_case to camelCase.
     */
    protected function denormalize($str)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    /**
     * Authenticate to Salesforce.
     *
     * @param boolean $exit If using web server authentication flow, true to make
     *                      the application exit right now (for the redirect to happen).
     *                      False if you don't want to exit now. Defaults to true.
     *
     * @throws \Exception
     */
    public function authenticate($exit = true)
    {
        if (empty($this->loginUrl)) {
            throw new \Exception("Missing loginUrl required for Salesforce authentication.");
        }

        // reponse_type: "code"
        if ($this->responseType === 'code') {
            $this->authorize($exit);
        }

        // grant_type: "password"
        if ($this->grantType === 'password') {
            $this->token();
        }
    }

    /**
     * Check the presence of some parameters and
     * throw an Exception with provided values if there is one missing.
     *
     * @param string[] $parameters Array of parameters to check in camel_case format.
     * @throws \Exception
     */
    protected function checkParameters($parameters)
    {
        foreach ($parameters as $parameter) {
            $property = $this->denormalize($parameter);

            if (empty($this->$property)) {
                $error = "Missing required URL parameter for Salesforce OAuth2." .
                    " Required parameters supplied are :";

                foreach ($parameters as $param) {
                    $prop = $this->denormalize($param);
                    $error .= ' ' . $param . ': ' . (is_null($this->$prop) ? 'null' : $this->$prop) . ',';
                }

                $error = ltrim($error, ',');
                throw new \Exception($error);
            }
        }
    }

    /**
     * Authorize to Salesforce.
     *
     * Request an Authorization code. This method sends a
     * redirect header to the Salesforce authorize url.
     *
     * @param boolean $exit True to make the application exit right now (for the redirect to happen).
     *                      False if you don't want to exit now. Defaults to true.
     * @throws \Exception
     */
    protected function authorize($exit = true)
    {
        $this->checkParameters(array(
            'response_type',
            'client_id',
            'redirect_uri'
        ));
        
        $uri = rtrim($this->loginUrl, '/') . '/services/oauth2/authorize?' .
            'response_type=' . $this->responseType .
            '&client_id=' . $this->clientId .
            '&redirect_uri=' . urlencode($this->redirectUri);

        header('Location: ' . $uri);

        if ($exit === true) {
            exit;
        }
    }

    /**
     * Request an access_token from Salesforce.
     * 
     * Use grant_type authentication code if a code is present
     * or fallback to username-password flow.
     */
    protected function token()
    {
        $uri = rtrim($this->loginUrl, '/') . '/services/oauth2/token';

        $http = new Client();
        $request = $http->createRequest('POST', $uri);
        $body = $request->getBody();

        // Authorization Flow with authorization_code
        if (!empty($this->code)) {
            $this->checkParameters(array(
                'client_id',
                'client_secret',
                'redirect_uri'
            ));
            
            $body->setField('grant_type', 'authorization_code');
            $body->setField('client_id', $this->clientId);
            $body->setField('client_secret', $this->clientSecret);
            $body->setField('redirect_uri', $this->redirectUri);
            $body->setField('code', $this->code);
        } else {
            // Fallback to username-password flow
            $this->checkParameters(array(
                'client_id',
                'client_secret',
                'username',
                'password'
            ));

            $body->setField('grant_type', 'password');
            $body->setField('client_id', $this->clientId);
            $body->setField('client_secret', $this->clientSecret);
            $body->setField('username', $this->username);
            $body->setField('password', $this->password);
        }

        $response = $http->send($request);
        $json = json_decode($response->getBody());

        $this->instance_url = $json->instance_url;
        $this->access_token = $json->access_token;
        $this->refresh_token = $json->refresh_token;
    }

    /**
     * Salesforce callback.
     *
     * When using web server authentication flow, after a successful
     * Salesforce authenticate(), set the $code and call this method to
     * request an instance_url, access_token and refresh_token.
     */
    public function callback()
    {
        $this->token();
    }

    /**
     * Refresh the Salesforce access_token.
     */
    public function refresh()
    {
        $this->checkParameters(array(
            'refresh_token',
            'client_id',
            'client_secret',
        ));

        $uri = rtrim($this->loginUrl, '/') . '/services/oauth2/token';

        $http = new Client();
        $request = $http->createRequest('POST', $uri);
        $body = $request->getBody();

        $body->setField('grant_type', 'refresh_token');
        $body->setField('refresh_token', $this->refreshToken);
        $body->setField('client_id', $this->clientId);
        $body->setField('client_secret', $this->clientSecret);

        $response = $http->send($request);
        $json = json_decode($response->getBody());

        $this->access_token = $json->access_token;
    }
}
