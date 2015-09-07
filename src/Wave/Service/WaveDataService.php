<?php

/**
 * Data Service for Wave.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Domain\OAuthUser;
use skyflow\Service\OAuthServiceInterface;
use skyflow\Service\RestOAuthAuthenticatedService;

use Salesforce\Domain\SalesforceUser;

/**
 * Data Service for Wave.
 */
class WaveDataService extends RestOAuthAuthenticatedService
{
    /**
     * Send a query to Wave.
     *
     * @param  string $query The query string.
     * @return string Response as string encoded in JSON format.
     */
    public function query($query)
    {
        $response = $this->httpPost('/query', array('query' => $query));
        return $response->json();
    }
}
