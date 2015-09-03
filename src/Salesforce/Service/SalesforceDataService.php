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
     * Send a SOQL query to Salesforce data API.
     *
     * @param  string $query The SOQL query string.
     * @return string Response as string encoded in JSON format.
     */
    public function query($query)
    {
        $response = $this->httpGet(
            "/query",
            array(
                "q" => rtrim($query, ';')
            ),
            array(
                'Authorization', 'OAuth ' . $this->getUser()->getAccessToken()
            )
        );

        return $response->json();
    }

    /**
     * Get SObjects metadata.
     *
     * @return string Response as string encoded in JSON format.
     */
    public function sobjects()
    {
        $response = $this->httpGet(
            "/sobjects",
            array(),
            array(
                'Authorization', 'OAuth ' . $this->getUser()->getAccessToken()
            )
        );

        return $response->json();
    }
}
