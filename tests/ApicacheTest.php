<?php

namespace Apicache;

use PHPUnit\Framework\TestCase;

/**
 * Class ApicacheTest
 *
 * Uses Apicache class to run some generic tests.
 */
class ApicacheTest extends TestCase
{
    private $key = '';
    private $baseUri = 'https://jsonplaceholder.typicode.com';
    private $timeout = 3;
    private $method = 'GET';
    private $path = '/todos';
    private $data = [];
    private $duration = 5 * 60;
    private $cacheDirectory = __DIR__ . '/../cache/';

    /**
     * Deletes cache file if it exists.
     *
     * @param string $path Path of the cache file.
     */
    private function purgeCache($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Generates unique hash based on data content.
     *
     * @param mixed $data
     * @return string
     */
    private function getCacheKey($data)
    {
        return sha1(md5(json_encode($data)));
    }

    /**
     * Creates service and returns todos.
     *
     * @param string $method GET in our case.
     * @param string $path API path like /todos.
     * @param array $data Data to send in body.
     * @param int $duration Cache duration time in seconds.
     * @param string $key API key.
     * @param string $baseUri API base uri.
     * @param int $timeout response timout.
     * @return mixed $response
     */
    private function getResponse($method, $path, array $data = [], int $duration = 5 * 60, $key = '', $baseUri = 'https://jsonplaceholder.typicode.com', $timeout = 3)
    {
        $service = new Apicache($key, $baseUri, $timeout);
        $response = $service->getTodos($method, $path, $data, $duration);

        return $response;
    }

    public function testGetTodos()
    {
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
        $response = $this->getResponse($this->method, $this->path, $this->data, $this->duration, $this->key, $this->baseUri, $this->timeout);
        $this->assertIsArray($response);
        $this->assertEquals(200, count($response));
    }

    public function testMethodException()
    {
        $this->expectException(Exceptions\ApicacheException::class);
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
        $this->getResponse('STRETCH', $this->path, $this->data, $this->duration, $this->key, $this->baseUri, $this->timeout);
    }

    public function testPathException()
    {
        $this->expectException(Exceptions\ApicacheException::class);
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
        $this->getResponse($this->method, '/wrong/path', $this->data, $this->duration, $this->key, $this->baseUri, $this->timeout);
    }

    public function testCacheIsCreated()
    {
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
        $this->getResponse($this->method, $this->path, $this->data, $this->duration, $this->key, $this->baseUri, $this->timeout);
        $this->assertFileExists($this->cacheDirectory . $this->getCacheKey($this->data));
        $cache = json_decode(file_get_contents($this->cacheDirectory . $this->getCacheKey($this->data)), true);
        $this->assertArrayHasKey('duration', $cache);
        $duration = $cache['duration'];
        $this->assertIsInt($duration);
        $this->assertEquals($this->duration, $duration - time());
        $this->assertIsArray($cache);
        $this->assertEquals(201, count($cache));
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
    }
}
