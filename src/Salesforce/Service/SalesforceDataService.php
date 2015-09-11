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
        $response = $this->httpGet('/query', array('q' => rtrim($query, ';')));

        $records = $response->json()['records'];
        $values = array();

        foreach ($records as $record) {
            unset($record['attributes']);
            array_push($values, $record);
        }

        return $values;
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
