<?php

/**
 * Service for the Salesforce data API.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Service\RestService;
use skyflow\Service\OAuthServiceInterface;

use Salesforce\Domain\SalesforceUser;

/**
 * Service for the Salesforce data API.
 */
class SalesforceDataService extends RestService
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
     * SalesforceDataService constructor.
     *
     * @param HttpClientInterface   $httpClient  An HTTP Client.
     * @param SalesforceUser        $user        The Salesforce OAuth user.
     * @param OAuthServiceInterface $authService The OAuth authentication service.
     */
    public function __construct(
        HttpClientInterface $httpClient,
        SalesforceUser $user,
        OAuthServiceInterface $authService
    ) {
        parent::__construct($httpClient);
        $this->user = $user;
        $this->authService = $authService;
        $this->setEndpoint($this->getUser()->getInstanceUrl() . '/services/data');
        $this->setVersion('v20.0');
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
     * Send a SOQL query to Salesforce data API.
     *
     * @param  string $query The SOQL query string.
     * @return string Response as string encoded in JSON format.
     */
    public function query($query)
    {
        try {
            $response = $this->httpGet(
                "/query",
                array(
                    "q" => rtrim($query, ';')
                ),
                array(
                    'Authorization', 'OAuth ' . $this->getUser()->getAccessToken()
                )
            );
        } catch (\Exception $ex) {
            if ($ex->getCode() === 401) {
                $this->getAuthService()->refresh();

                $response = $this->httpGet(
                    "/query",
                    array(
                        "q" => rtrim($query, ';')
                    ),
                    array(
                        'Authorization' => 'OAuth ' . $this->getUser()->getAccessToken()
                    )
                );
            }
        }

        $data = $response->json();
        $data = json_encode($data);

        return $data;
    }
}
