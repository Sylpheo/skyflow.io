<?php

/**
 * Service for the Salesforce data API.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Domain\OAuthUser;
use skyflow\Service\OAuthServiceInterface;
use skyflow\Service\RestOAuthAuthenticatedService;

use Salesforce\Domain\SalesforceUser;

/**
 * Service for the Salesforce data API.
 */
class SalesforceDataService extends RestOAuthAuthenticatedService
{
    /**
     * SalesforceDataService constructor.
     *
     * We need a SalesforceUser because we need the Salesforce instance url which
     * is specific to Salesforce.
     *
     * @param HttpClientInterface   $httpClient  An HTTP Client.
     * @param SalesforceUser        $user        The Salesforce OAuth user.
     * @param OAuthServiceInterface $authService The OAuth authentication service.
     */
    public function __construct(
        HttpClientInterface $httpClient,
        OAuthUser $user,
        OAuthServiceInterface $authService
    ) {
        parent::__construct($httpClient, $user, $authService);

        $this->setProvider('Salesforce');
        $this->setEndpoint($this->getUser()->getInstanceUrl() . '/services/data');
        $this->setVersion('v20.0');
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

        return $response->json();
    }
}
