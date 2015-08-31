<?php

/**
 * Service for the Salesforce data API.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use Salesforce\Domain\SalesforceUser;
use skyflow\Service\OAuthServiceInterface;

/**
 * Service for the Salesforce data API.
 */
class SalesforceDataService
{
    /**
     * The Salesforce OAuth user.
     *
     * We have to use the SalesforceUser because we need the instance_url.
     *
     * @var SalesforceUser
     */
    private $user;

    /**
     * The OAuth authentication service in case we need to refresh the access_token.
     *
     * We can use the OAuthServiceInterface instead of the SalesforceOAuthService
     * because we may only need to refresh the access_token, nothing to care about
     * the instance_url.
     *
     * @var OAuthServiceInterface
     */
    private $authService;

    /**
     * Guzzle HTTP client.
     *
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * SalesforceDataService constructor.
     *
     * @param SalesforceUser        $user        The Salesforce OAuth user.
     * @param OAuthServiceInterface $authService The OAuth authentication service.
     * @param HttpClientInterface   $httpClient  An HTTP Client.
     */
    public function __construct(
        SalesforceUser $user,
        OAuthServiceInterface $authService,
        HttpClientInterface $httpClient
    ) {
        $this->user = $user;
        $this->authService = $authService;
        $this->httpClient = $httpClient;
    }

    /**
     * Get the Salesforce OAuth user.
     *
     * @return SalesforceUser The Salesforce OAuth user.
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * Get the OAuth authentication service.
     *
     * @return OAuthServiceInterface The OAuth authentication service.
     */
    protected function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Get the HTTP client.
     *
     * @return HttpClientInterface The HTTP client.
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Send a SOQL request to Salesforce data API.
     *
     * @param  string $query The SOQL request string.
     * @return string Response as string encoded in JSON format.
     */
    public function soql($query)
    {
        $accessToken = $this->getUser()->getAccessToken();
        $instanceUrl = $this->getUser()->getInstanceUrl();

        $salesforceRequest = $this->getHttpClient()->createRequest(
            'GET',
            $instanceUrl . "/services/data/v20.0/query?q=" . urlencode($query)
        );

        $salesforceRequest->setHeader(
            'Authorization',
            'Bearer: ' . $accessToken
        );

        $response = null;
        $statuscode = null;

        try {
            $response = $this->getHttpClient()->send($salesforceRequest);
            $statuscode = $response->getStatusCode();
        } catch (\Exception $e) {
            $statuscode = $e->getCode();
        }

        if ($statuscode == '401') {
            $this->getAuthService()->refresh();

            // Resend request
            $salesforceRequest->setHeader(
                'Authorization',
                'Bearer: ' . $this->getUser()->getRefreshToken()
            );

            $response = $this->getHttpClient()->send($salesforceRequest);
        }

        $data = $response->json();
        var_dump($data);
        exit;
        $data = json_encode($data);

        return $data;
    }
}
