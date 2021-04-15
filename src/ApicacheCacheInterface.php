<?php

namespace Apicache;

use Apicache\CacheInterface;

/**
 * Class ApicacheCacheInterface
 *
 * Handles caching using CacheInterface interface.
 */
class ApicacheCacheInterface implements CacheInterface
{
    private $directory = __DIR__ . '/../cache/';

    /**
     * Store a mixed type value in cache for a certain amount of seconds.
     * Supported values should be scalar types and arrays.
     *
     * @param string $key
     * @param mixed $value
     * @param int $duration Duration in seconds
     * @return mixed
     */
    public function set(string $key, $value, int $duration)
    {
        $file = $this->directory . $key;
        $value['duration'] = time() + $duration;

        file_put_contents($file, json_encode($value));

        return $value;
    }

    /**
     * Retrieve stored item.
     * Returns the same type as it was stored in.
     * Returns null if entry has expired.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        $path = $this->directory . $key;

        if (file_exists($path)) {
            $file = file_get_contents($this->directory . $key);
            $value = json_decode($file, true);
            $duration = ($value && $value['duration']) ? $value['duration'] : null;

            if ($duration && $duration > time()) {
                unset($value['duration']);

                return $value;
            }
        }
    }
}
