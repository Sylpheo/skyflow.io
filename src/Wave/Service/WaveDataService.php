<?php

/**
 * Data Service for Wave.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use Skyflow\Domain\OAuthUser;
use Skyflow\Service\OAuthServiceInterface;
use Skyflow\Service\RestOAuthAuthenticatedService;

use Salesforce\Domain\SalesforceUser;

/**
 * Data Service for Wave.
 */
class WaveDataService extends RestOAuthAuthenticatedService
{
    /**
     * Executes a query written in the Salesforce Analytics Query Language (SAQL).
     *
     * @param  string $query The query string.
     * @return string Response as string encoded in JSON format.
     */
    public function query($query)
    {
        $response = $this->httpPost('/query', array('query' => $query));
        return $response->json();
    }

    /**
     * Returns a list of Analytics Cloud datasets.
     *
     * @return array List of Analytics Cloud datasets.
     */
    public function datasets()
    {
        $response = $this->httpGet('/datasets');
        return $response->json();
    }
}
