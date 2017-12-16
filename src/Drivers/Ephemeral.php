<?php

namespace PHLAK\Stash\Drivers;

use PHLAK\Stash\Item;

class Ephemeral extends Driver
{
    /** @var array Array of cached items */
    protected $cache = [null => []];

    /** @var string Prefix string to prevent collisions */
    protected $prefix = '';

    /**
     * Stash\Drivers\Ephemeral constructor, runs on object creation.
     *
     * @param \Closure|null $closure Anonymous configuration function
     */
    public function __construct(\Closure $closure = null)
    {
        if (is_callable($closure)) {
            $this->prefix = $closure()['prefix'];
        }
    }

    /**
     * Put an item into the cache for a specified duration.
     *
     * @param string $key     Unique item identifier
     * @param mixed  $data    Data to cache
     * @param int    $minutes Time in minutes until item expires
     *
     * @return bool True on sucess, otherwise false
     */
    public function put($key, $data, $minutes = 0)
    {
        $this->cache[$this->prefix][$key] = new Item($data, $minutes);

        return true;
    }

    /**
     * Put an item into the cache permanently.
     *
     * @param string $key  Unique identifier
     * @param mixed  $data Data to cache
     *
     * @return bool True on sucess, otherwise false
     */
    public function forever($key, $data)
    {
        return $this->put($key, $data);
    }

    /**
     * Get an item from the cache.
     *
     * @param string $key     Uniqe item identifier
     * @param mixed  $default Default data to return
     *
     * @return mixed Cached data or $default value
     */
    public function get($key, $default = false)
    {
        if (array_key_exists($key, $this->cache[$this->prefix])) {
            $item = $this->cache[$this->prefix][$key];
            if ($item->notExpired()) {
                return $item->data;
            }
        }

        return $default;
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
        if (array_key_exists($key, $this->cache[$this->prefix])) {
            $item = $this->cache[$this->prefix][$key];

            return $item->notExpired();
        }

        return false;
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
     * @param int    $value The ammount by which to increment
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function increment($key, $value = 1)
    {
        if (array_key_exists($key, $this->cache[$this->prefix])) {
            $item = $this->cache[$this->prefix][$key];

            return $item->increment($value);
        }

        return false;
    }

    /**
     * Decrease the value of a stored integer.
     *
     * @param string $key   Unique item identifier
     * @param int    $value The ammount by which to decrement
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function decrement($key, $value = 1)
    {
        return $this->increment($key, $value * -1);
    }

    /**
     * Set a new expiration time for an item in the cache.
     *
     * @param string $key     Unique item identifier
     * @param int    $minutes Time in minutes until item expires
     *
     * @return bool True on success, otherwise false
     */
    public function touch($key, $minutes = 0)
    {
        return $this->put($key, $this->get($key), $minutes);
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
        if (array_key_exists($key, $this->cache[$this->prefix])) {
            unset($this->cache[$this->prefix][$key]);

            return true;
        }

        return false;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool True on success, otherwise false
     */
    public function flush()
    {
        $this->cache[$this->prefix] = [];

        return true;
    }
}
