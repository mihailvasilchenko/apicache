<?php

namespace Apicache;

use Apicache\ApicacheApiClient;
use Apicache\ApicacheCacheInterface;

/**
 * Class Apicache
 *
 * Uses ApicacheApiClient class to retrieve data and ApicacheCacheInterface class to handle caching.
 */
class Apicache
{
    private $api;
    private $cache;

    /**
     * Setup client and cache.
     *
     * @param string $key API key.
     * @param string $baseUri API base uri.
     * @param int $timeout response max timeout.
     */
    public function __construct($key, $baseUri, $timeout)
    {
        $this->api = new ApicacheApiClient($key, $baseUri, $timeout);
        $this->cache = new ApicacheCacheInterface();
    }

    /**
     * Get todos.
     *
     * @param string $method GET in our case.
     * @param string $path API path like /todos.
     * @param array $data Data to send in body.
     * @param int $duration Cache duration time in seconds.
     * @return mixed $response
     */
    public function getTodos($method, $path, array $data = [], int $duration = 5 * 60)
    {
        $key = sha1(md5(json_encode($data)));

        $todos = $this->cache->get($key);

        if ($todos) {
            return $todos;
        }

        $todos = $this->api->request($method, $path, $data);

        $this->cache->set($key, $todos, 5 * 60);

        return $todos;
    }
}
