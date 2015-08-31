<?php

/**
 * Data Service for Wave.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

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
        SalesforceUser $user,
        OAuthServiceInterface $authService,
        HttpClientInterface $httpClient
    ) {
        parent::__construct($user, $authService, $httpClient);
        $this->setEndPoint('/services/data');
        $this->setVersion('v24.0');
    }

    /**
     * {@inheritdoc}
     */
    public function soql($query)
    {
        throw new UnsupportedOperationException();
    }

    /**
     * Send a request to Wave.
     *
     * @param string $request The request string.
     * @return string Response as string encoded in JSON format.
     */
    public function saql($request)
    {
        $waveRequest = $this->getHttpClient()->createRequest(
            'POST',
            $this->getUser()->getInstanceUrl() . $this->getEndpoint() . "/" . $this->getVersion() . "/wave/query",
            [
                'json' => [
                    'query' => $request
                ]
            ]
        );

        $waveRequest->setHeader('Content-Type', 'application/json');
        $waveRequest->setHeader('Authorization', 'Bearer ' . $this->getUser()->getAccessToken());
        $response = $this->getHttpClient()->send($waveRequest);
        $responseBody = json_decode($response->getBody());
        $data = $response->json();
        $data = json_encode($data);

        return $data;
    }
}
