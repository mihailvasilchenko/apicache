<?php

namespace Apicache;

use PHPUnit\Framework\TestCase;

/**
 * Class ApicacheCacheInterfaceTest
 *
 * Uses ApicacheCacheInterface class to run some generic tests.
 */
class ApicacheCacheInterfaceTest extends TestCase
{
    private $value = [
        0 => [
            'userId' => 1, 
            'id' => 1, 
            'title' => 'delectus aut autem', 
            'completed' => false, 
        ],
    ];
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

    public function testSet()
    {
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
        $cache = new ApicacheCacheInterface();
        $cache->set($this->getCacheKey($this->data), $this->value, $this->duration);
        $this->assertFileExists($this->cacheDirectory . $this->getCacheKey($this->data));
        $file = json_decode(file_get_contents($this->cacheDirectory . $this->getCacheKey($this->data)), true);
        $this->assertArrayHasKey('duration', $file);
        $duration = $file['duration'];
        $this->assertIsInt($duration);
        $this->assertEquals($this->duration, $duration - time());
        $this->value['duration'] = $duration;
        $this->assertEquals($this->value, $file);
    }

    public function testGet()
    {
        $cache = new ApicacheCacheInterface();
        $file = $cache->get($this->getCacheKey($this->data));
        $this->assertEquals($this->value, $file);
    }

    public function testGetReturnsNullIfDurationExpired()
    {
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
        $cache = new ApicacheCacheInterface();
        $cache->set($this->getCacheKey($this->data), $this->value, 0);
        $file = $cache->get($this->getCacheKey($this->data));
        $this->assertNull($file);
    }

    public function testGetReturnsNullIfNoFileExists()
    {
        $this->purgeCache($this->cacheDirectory . $this->getCacheKey($this->data));
        $cache = new ApicacheCacheInterface();
        $file = $cache->get($this->getCacheKey($this->data));
        $this->assertNull($file);
    }
}
