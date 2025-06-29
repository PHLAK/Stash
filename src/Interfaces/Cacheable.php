<?php

namespace PHLAK\Stash\Interfaces;

use Closure;

interface Cacheable
{
    /**
     * Put an item into the cache for a specified duration.
     *
     * @param string $key Unique item identifier
     * @param mixed $data Data to cache
     * @param int $minutes Time in minutes until item expires (default: 0)
     *
     * @return bool True on success, otherwise false
     */
    public function put(string $key, mixed $data, int $minutes = 0): bool;

    /**
     * Put an item into the cache permanently.
     *
     * @param string $key Unique identifier
     * @param mixed $data Data to cache
     *
     * @return bool True on success, otherwise false
     */
    public function forever(string $key, mixed $data): bool;

    /**
     * Get an item from the cache.
     *
     * @param string $key Unique item identifier
     * @param mixed $default Default data to return (default: false)
     *
     * @return mixed Cached data or $default value
     */
    public function get(string $key, mixed $default = false): mixed;

    /**
     * Check if an item exists in the cache.
     *
     * @param string $key Unique item identifier
     *
     * @return bool True if item exists, otherwise false
     */
    public function has(string $key): bool;

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results for a
     * specified duration.
     *
     * @param string $key Unique item identifier
     * @param int $minutes Time in minutes until item expires
     * @param Closure $closure Anonymous closure function
     *
     * @return mixed Cached data or $closure results
     */
    public function remember(string $key, int $minutes, Closure $closure): mixed;

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results permanently.
     *
     * @param string $key Unique item identifier
     * @param Closure $closure Anonymous closure function
     *
     * @return mixed Cached data or $closure results
     */
    public function rememberForever(string $key, Closure $closure): mixed;

    /**
     * Increase the value of a stored integer.
     *
     * @param string $key Unique item identifier
     * @param int $value The amount by which to increment
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function increment(string $key, int $value = 1): mixed;

    /**
     * Decrease the value of a stored integer.
     *
     * @param string $key Unique item identifier
     * @param int $value The amount by which to decrement
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function decrement(string $key, int $value = 1): mixed;

    /**
     * Set a new expiration time for an item in the cache.
     *
     * @param string|string[] $key Unique item identifier
     * @param int $minutes Time in minutes until item expires
     *
     * @return bool True on success, otherwise false
     */
    public function touch(array|string $key, int $minutes = 0): bool;

    /**
     * Permanently remove an item from the cache.
     *
     * @param string|string[] $key Unique item identifier
     *
     * @return bool True on success, otherwise false
     */
    public function forget(array|string $key): bool;

    /**
     * Remove all items from the cache.
     *
     * @return bool True on success, otherwise false
     */
    public function flush(): bool;
}
