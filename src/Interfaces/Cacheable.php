<?php

namespace PHLAK\Stash\Interfaces;

use Closure;

interface Cacheable
{
    /**
     * Put an item into the cache for a specified duration.
     *
     * @param string $key     Unique item identifier
     * @param mixed  $data    Data to cache
     * @param int    $minutes Time in minutes until item expires (default: 0)
     *
     * @return bool True on success, otherwise false
     */
    public function put($key, $data, $minutes = 0);

    /**
     * Put an item into the cache permanently.
     *
     * @param string $key  Unique identifier
     * @param mixed  $data Data to cache
     *
     * @return bool True on success, otherwise false
     */
    public function forever($key, $data);

    /**
     * Get an item from the cache.
     *
     * @param string $key     Unique item identifier
     * @param mixed  $default Default data to return (default: false)
     *
     * @return mixed Cached data or $default value
     */
    public function get($key, $default = false);

    /**
     * Check if an item exists in the cache.
     *
     * @param string $key Unique item identifier
     *
     * @return bool True if item exists, otherwise false
     */
    public function has($key);

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results for a
     * specified duration.
     *
     * @param string  $key     Unique item identifier
     * @param int     $minutes Time in minutes until item expires
     * @param Closure $closure Anonymous closure function
     *
     * @return mixed Cached data or $closure results
     */
    public function remember($key, $minutes, Closure $closure);

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results permanently.
     *
     * @param string  $key     Unique item identifier
     * @param Closure $closure Anonymous closure function
     *
     * @return mixed Cached data or $closure results
     */
    public function rememberForever($key, Closure $closure);

    /**
     * Increase the value of a stored integer.
     *
     * @param string $key   Unique item identifier
     * @param int    $value The amount by which to increment
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function increment($key, $value = 1);

    /**
     * Decrease the value of a stored integer.
     *
     * @param string $key   Unique item identifier
     * @param int    $value The amount by which to decrement
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function decrement($key, $value = 1);

    /**
     * Set a new expiration time for an item in the cache.
     *
     * @param array|string $key     Unique item identifier
     * @param int          $minutes Time in minutes until item expires
     *
     * @return bool True on success, otherwise false
     */
    public function touch($key, $minutes = 0);

    /**
     * Permanently remove an item from the cache.
     *
     * @param string $key Unique item identifier
     *
     * @return bool True on success, otherwise false
     */
    public function forget($key);

    /**
     * Remove all items from the cache.
     *
     * @return bool True on success, otherwise false
     */
    public function flush();
}
