<?php

namespace Apicache;

use PHPUnit\Framework\TestCase;

/**
 * Class ApicacheApiClientTest
 *
 * Uses ApicacheApiClient class to run some generic tests.
 */
class ApicacheApiClientTest extends TestCase
{
    private $key = '';
    private $method = 'GET';
    private $path = '/todos';
    private $data = [];

    /**
     * Creates the client and returns todos.
     *
     * @param string $method POST in our case.
     * @param string $path API path like /todos.
     * @param array $data Data to send in body.
     * @param string $key API key without base64 encoding.
     * @return mixed $response
     */
    private function getResponse($method, $path, array $data = [], $key = '')
    {
        $client = new ApicacheApiClient($key);
        $response = $client->request($method, $path, $data);

        return $response;
    }

    public function testGetTodos()
    {
        $response = $this->getResponse($this->method, $this->path, $this->data, $this->key);
        $this->assertIsArray($response);
        $this->assertEquals(200, count($response));
    }

    public function testMethodException()
    {
        $this->expectException(Exceptions\ApicacheException::class);
        $this->getResponse('STRETCH', $this->path, $this->data, $this->key);
    }

    public function testPathException()
    {
        $this->expectException(Exceptions\ApicacheException::class);
        $this->getResponse($this->method, '/wrong/path', $this->data, $this->key);
    }
}
