<?php

namespace Stash;

use Stash\Interfaces\Cacheable;

class Memcache implements Cacheable {

    /** @var object Instance of Memcache */
    protected $memcache;

    /**
     * Stash\Memcache constructor, runs on object creation
     *
     * @param string $host Memcache server host
     * @param string $port Memcache server port
     */
    public function __construct($host = 'localhost', $port = 11211) {
        $this->memcache = new \Memcache;
        $this->memcache->connect($host, $port);
    }

    /**
     * Put an item into the cache for a specified duration
     *
     * @param  string $key     Unique item identifier
     * @param  mixed  $data    Data to cache
     * @param  int    $minutes Minutes to cache
     *
     * @return bool            True on sucess, otherwise false
     */
    public function put($key, $data, $minutes = 0) {
        return $this->memcache->set($key, $data, 0, ($minutes * 60));
    }

    /**
     * Put an item into the cache permanently
     *
     * @param  string $key  Unique identifier
     * @param  mixed  $data Data to cache
     *
     * @return bool         True on sucess, otherwise false
     */
    public function forever($key, $data) {
        return $this->put($key, $data);
    }

    /**
     * Get an item from the cache
     *
     * @param  string $key     Uniqe item identifier
     * @param  mixex  $default Default data to return
     *
     * @return mixed           Cached data or null
     */
    public function get($key, $default = null) {
        return $this->memcache->get($key) ?: $default;
    }

    /**
     * Check if an item exists in the cache
     *
     * @param  string $key Unique item identifier
     *
     * @return bool        True if item exists, otherwise false
     */
    public function has($key) {
        return $this->memcache->get($key) ? true : false;
    }

    /**
     * Retrieve item from cache or add it to the cache if it doesn't exist
     *
     * @param  string $key     Unique item identifier
     * @param  int    $minutes Minutes to cache
     * @param  mixed  $data    Data to cache
     *
     * @return mixed           Cached data
     */
    public function remember($key, $minutes, \Closure $data) {

        $cache = $this->get($key);

        if ($cache == null) {
            $this->put($key, $data(), $minutes);
            return $data();
        }

        return $cache;

    }

    /**
     * Removes an item from the cache
     *
     * @param  string $key Unique item identifier
     *
     * @return bool        True on success, otherwise false
     */
    public function forget($key) {
        return $this->memcache->delete($key);
    }

}
