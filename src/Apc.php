<?php

namespace Stash;

use Stash\Interfaces\Cacheable;

class Apc implements Cacheable {

    /** @var string Key prefix for preventing collisions */
    protected $prefix;

    /**
     * Stash\Apc constructor, runs on object creation
     *
     * @param string $prefix Key prefix for preventing collisions
     */
    public function __construct($prefix = '') {
        $this->prefix = $prefix;
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
        return apc_store($this->prefix($key), $data, ($minutes * 60));
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
     * @return mixed           Cached data or $default value
     */
    public function get($key, $default = false) {
        return apc_fetch($this->prefix($key)) ?: $default;
    }

    /**
     * Check if an item exists in the cache
     *
     * @param  string $key Unique item identifier
     *
     * @return bool        True if item exists, otherwise false
     */
    public function has($key) {
        return apc_exists($this->prefix($key));
    }

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results for a
     * specified duration
     *
     * @param  string $key     Unique item identifier
     * @param  int    $minutes Minutes to cache
     * @param  mixed  $closure Anonymous closure function
     *
     * @return mixed           Cached data or $closure results
     */
    public function remember($key, $minutes, \Closure $closure) {

        if ($cache = $this->get($key)) {
            return $cache;
        }

        $data = $closure();

        if ($this->put($key, $data, $minutes)) {
            return $data;
        }

        return false;

    }

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results permanently
     *
     * @param  string $key     Unique item identifier
     * @param  mixed  $closure Anonymous closure function
     *
     * @return mixed           Cached data or $closure results
     */
    public function rememberForever($key, \Closure $closure) {

        if ($cache = $this->get($key)) {
            return $cache;
        }

        $data = $closure();

        if ($this->forever($key, $data)) {
            return $data;
        }

        return false;

    }

    /**
     * Removes an item from the cache
     *
     * @param  string $key Unique item identifier
     *
     * @return bool        True on success, otherwise false
     */
    public function forget($key) {
        return apc_delete($this->prefix($key));
    }

    /**
     * Get prefixed key
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    protected function prefix($key) {
        if (! empty($this->prefix)) return $this->prefix . '_' . $key;
        return $key;
    }

}
