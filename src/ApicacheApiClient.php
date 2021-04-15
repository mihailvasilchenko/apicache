<?php

namespace Apicache;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Apicache\Exceptions\ApicacheException;

/**
 * Class ApicacheApiClient
 *
 * Sets up the GuzzleHttp Client class and handles requests to API.
 */
class ApicacheApiClient
{
    private $client;
    private $key;
    private $baseUri;
    private $timeout;

    /**
     * Setup key and client.
     *
     * @param string $key API key.
     * @param string $baseUri API base uri.
     * @param int $timeout response max timeout.
     */
    public function __construct($key = '', $baseUri = 'https://jsonplaceholder.typicode.com', $timeout = 3)
    {
        $this->key = $key;
        $this->baseUri = $baseUri;
        $this->timeout = $timeout;
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
        ]);
    }

    /**
     * Request API data.
     *
     * @param string $method GET in our case.
     * @param string $path API path like /todos.
     * @param array $data Data to send in body.
     * @return mixed $response
     * @throws \Apicache\Exceptions\ApicacheException
     */
    public function request($method, $path, array $data = [])
    {
        try {
            $response = $this->client->request($method, $path, [
                'headers' => [
                    'Authorization' => 'Basic ' . $this->key,
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($data, true),
            ]);
        } catch (GuzzleException $e) {
            throw new ApicacheException($e->getMessage());
        }

        $response = json_decode((string) $response->getBody(), true);

        return $response;
    }
}
