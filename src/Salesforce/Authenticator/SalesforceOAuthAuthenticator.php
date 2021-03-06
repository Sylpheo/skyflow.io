<?php

/**
 * Saleforce OAuth authenticator.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Authenticator;

use Skyflow\Authenticator\AbstractOAuthAuthenticator;

/**
 * Salesforce OAuth authenticator.
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
 * ```
 */
class SalesforceOAuthAuthenticator extends AbstractOAuthAuthenticator
{
    /**
     * Salesforce login URL.
     *
     * For example: "https://login.salesforce.com/" or
     * "https://test.salesforce.com/". May or may not contain trailing slash.
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
     * {@inheritdoc}
     *
     * @throws \Exception If there are required parameters missing.
     */
    public function authenticate($exit = true)
    {
        if (empty($this->loginUrl)) {
            throw new \Exception(
                "Missing loginUrl required for Salesforce authentication."
            );
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
     * {@inheritdoc}
     */
    public function callback()
    {
        $this->token();
    }

    /**
     * {@inheritdoc}
     */
    public function refresh()
    {
        $this->checkParameters(array(
            'refresh_token',
            'client_id',
            'client_secret',
        ));

        $uri = rtrim($this->loginUrl, '/') . '/services/oauth2/token';

        $request = $this->getHttpClient()->createRequest('POST', $uri);
        $body = $request->getBody();

        $body->setField('grant_type', 'refresh_token');
        $body->setField('refresh_token', $this->refreshToken);
        $body->setField('client_id', $this->clientId);
        $body->setField('client_secret', $this->clientSecret);

        $response = $this->getHttpClient()->send($request);
        $json = json_decode($response->getBody());

        $this->accessToken = $json->access_token;
    }

    /**
     * Authorize to Salesforce.
     *
     * Request an Authorization code. This method sends a
     * redirect header to the Salesforce authorize url.
     *
     * @param boolean $exit True to make the application exit right now
     *                      (for the redirect to happen). False if you don't
     *                      want to exit now. Defaults to true.
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

        $request = $this->getHttpClient()->createRequest('POST', $uri);
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

        $response = $this->getHttpClient()->send($request);
        $json = json_decode($response->getBody());

        $this->instance_url = $json->instance_url;
        $this->access_token = $json->access_token;
        $this->refresh_token = $json->refresh_token;
    }
}
