<?php

/**
 * Service for Wave.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Service;

use GuzzleHttp\ClientInterface;

use skyflow\Domain\Users;

class WaveService
{
    /**
     * The skyflow logged-in user.
     *
     * @var Users
     */
    protected $user;

    /**
     * Guzzle HTTP client.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * WaveService constructor.
     *
     * @param Users           $user       The skyflow logged-in user.
     * @param ClientInterface $httpClient A Guzzle HTTP Client.
     */
    public function __construct(Users $user, ClientInterface $httpClient)
    {
        $this->user = $user;
        $this->httpClient = $httpClient;
    }

    /**
     * Send a request to Wave.
     *
     * @param string $request The request string.
     * @return string Response as string encoded in JSON format.
     */
    public function request($request)
    {
        $waveRequest = $this->httpClient->createRequest(
            'POST',
            $this->instance_url . '/services/data/v34.0/wave/query',
            [
                'json' => [
                    'query' => $request
                ]
            ]
        );

        $waveRequest->setHeader('Content-Type', 'application/json');
        $waveRequest->setHeader('Authorization', 'Bearer ' . $this->user->getWaveAccessToken());
        $response = $this->httpClient->send($waveRequest);
        $responseBody = json_decode($response->getBody());
        $data = $response->json();
        $data = json_encode($data);

        return $data;
    }
}
