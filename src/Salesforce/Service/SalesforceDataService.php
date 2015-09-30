<?php

/**
 * Service for the Salesforce data API.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use Skyflow\Domain\OAuthUser;
use Skyflow\Service\OAuthServiceInterface;
use Skyflow\Service\RestOAuthAuthenticatedService;

use Salesforce\Domain\SalesforceUser;
use Salesforce\Service\Data\SalesforceQueryService;

/**
 * Service for the Salesforce data API.
 */
class SalesforceDataService extends RestOAuthAuthenticatedService
{
    /**
     * Send a SOQL query to Salesforce data API.
     *
     * @param  string  $query   The SOQL query string.
     * @param  boolean $inherit Whether the new query must inherit its parent query.
     * @return string  Response as string encoded in JSON format.
     */
    public function query($query, $inherit = false)
    {
        $queryService = $this->getService('query');
        return SalesforceQueryService::query($queryService, $query, $inherit);
    }

    /**
     * Get SObjects metadata.
     *
     * @return string Response as string encoded in JSON format.
     */
    public function sobjects()
    {
        $response = $this->httpGet('/sobjects');
        return $response->json();
    }
}
