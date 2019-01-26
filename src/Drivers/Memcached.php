<?php

namespace PHLAK\Stash\Drivers;

use PHLAK\Stash\Interfaces\Cacheable;
use Closure;

class Memcached implements Cacheable
{
    /** @var \Memcached Instance of Memcached */
    protected $memcached;

    /**
     * Create a Memcached cache driver object.
     *
     * @param \Closure $closure Anonymous configuration function
     */
    public function __construct(Closure $closure)
    {
        $this->memcached = new \Memcached;

        $closure($this->memcached);
    }

    /**
     * Put an item into the cache for a specified duration.
     *
     * @param string $key     Unique item identifier
     * @param mixed  $data    Data to cache
     * @param int    $minutes Time in minutes until item expires
     *
     * @return bool True on success, otherwise false
     */
    public function put($key, $data, $minutes = 0)
    {
        $expiration = $minutes == 0 ? 0 : time() + ($minutes * 60);

        return $this->memcached->set($key, $data, $expiration);
    }

    /**
     * Put an item into the cache permanently.
     *
     * @param string $key  Unique identifier
     * @param mixed  $data Data to cache
     *
     * @return bool True on success, otherwise false
     */
    public function forever($key, $data)
    {
        return $this->put($key, $data);
    }

    /**
     * Get an item from the cache.
     *
     * @param string $key     Unique item identifier
     * @param mixed  $default Default data to return
     *
     * @return mixed Cached data or $default value
     */
    public function get($key, $default = false)
    {
        return $this->memcached->get($key) ?: $default;
    }

    /**
     * Check if an item exists in the cache.
     *
     * @param string $key Unique item identifier
     *
     * @return bool True if item exists, otherwise false
     */
    public function has($key)
    {
        $this->memcached->get($key);

        return $this->memcached->getResultCode() == \Memcached::RES_SUCCESS;
    }

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results for a
     * specified duration.
     *
     * @param string $key     Unique item identifier
     * @param int    $minutes Time in minutes until item expires
     * @param mixed  $closure Anonymous closure function
     *
     * @return mixed Cached data or $closure results
     */
    public function remember($key, $minutes, \Closure $closure)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $data = $closure();

        return $this->put($key, $data, $minutes) ? $data : false;
    }

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results permanently.
     *
     * @param string $key     Unique item identifier
     * @param mixed  $closure Anonymous closure function
     *
     * @return mixed Cached data or $closure results
     */
    public function rememberForever($key, \Closure $closure)
    {
        return $this->remember($key, 0, $closure);
    }

    /**
     * Increase the value of a stored integer.
     *
     * @param string $key   Unique item identifier
     * @param int    $value The amount by which to increment
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function increment($key, $value = 1)
    {
        return $this->memcached->increment($key, $value);
    }

    /**
     * Decrease the value of a stored integer.
     *
     * @param string $key   Unique item identifier
     * @param int    $value The amount by which to decrement
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function decrement($key, $value = 1)
    {
        return $this->memcached->decrement($key, $value);
    }

    /**
     * Set a new expiration time for an item in the cache.
     *
     * @param string|array $key     Unique item identifier
     * @param int          $minutes Time in minutes until item expires
     *
     * @return bool True on success, otherwise false
     */
    public function touch($key, $minutes = 0)
    {
        if (is_array($key)) {
            return array_walk($key, function ($key) use ($minutes) {
                $this->touch($key, $minutes);
            });
        }

        if (! $this->has($key)) {
            return $this->put($key, false);
        }

        return $this->memcached->touch($key, $minutes);
    }

    /**
     * Removes an item from the cache.
     *
     * @param string $key Unique item identifier
     *
     * @return bool True on success, otherwise false
     */
    public function forget($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool True on success, otherwise false
     */
    public function flush()
    {
        return $this->memcached->flush();
    }
}
