<?php

/**
 * Data Service for Wave.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Service\OAuthServiceInterface;

use Salesforce\Domain\SalesforceUser;
use Salesforce\Service\SalesforceDataService;

/**
 * Data Service for Wave.
 */
class WaveDataService extends SalesforceDataService
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        HttpClientInterface $httpClient,
        SalesforceUser $user,
        OAuthServiceInterface $authService
    ) {
        parent::__construct($httpClient, $user, $authService);
        $this->setEndpoint($this->getUser()->getInstanceUrl() . '/services/data/v34.0/wave/query');
        $this->setVersion(null);
    }

    /**
     * Send a query to Wave.
     *
     * @param  string $query The query string.
     * @return string Response as string encoded in JSON format.
     */
    public function query($query)
    {
        try {
            $response = $this->httpPost(
                null,
                [
                    'json' => [
                        'query' => $query
                    ]
                ],
                array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getUser()->getAccessToken()
                )
            );
        } catch (\Exception $ex) {
            if ($ex->getCode() === 401) {
                $this->getAuthService()->refresh();

                $response = $this->httpPost(
                    null,
                    [
                        'json' => [
                            'query' => $query
                        ]
                    ],
                    array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->getUser()->getAccessToken()
                    )
                );
            }
        }

        return $response->json();
    }
}
