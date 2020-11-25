<?php

namespace PHLAK\Stash\Drivers;

use Closure;
use PHLAK\Stash\Interfaces\Cacheable;
use RuntimeException;

class APCu implements Cacheable
{
    /** @var string Prefix string to prevent collisions */
    protected $prefix = '';

    /**
     * Create an APCu cache driver object.
     *
     * @param \Closure|null $closure Anonymous configuration function
     */
    public function __construct(Closure $closure = null)
    {
        if (is_callable($closure)) {
            $closure = $closure->bindTo($this, self::class);

            if (! $closure) {
                throw new RuntimeException('Failed to bind closure');
            }

            $closure();
        }
    }

    /**
     * Put an item into the cache for a specified duration.
     *
     * @param string $key Unique item identifier
     * @param mixed $data Data to cache
     * @param int $minutes Time in minutes until item expires
     *
     * @return bool True on success, otherwise false
     */
    public function put($key, $data, $minutes = 0)
    {
        return apcu_store($this->prefix($key), $data, ($minutes * 60));
    }

    /**
     * Put an item into the cache permanently.
     *
     * @param string $key Unique identifier
     * @param mixed $data Data to cache
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
     * @param string $key Unique item identifier
     * @param mixed $default Default data to return
     *
     * @return mixed Cached data or $default value
     */
    public function get($key, $default = false)
    {
        return apcu_fetch($this->prefix($key)) ?: $default;
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
        return apcu_exists($this->prefix($key));
    }

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results for a
     * specified duration.
     *
     * @param string $key Unique item identifier
     * @param int $minutes Time in minutes until item expires
     * @param mixed $closure Anonymous closure function
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
     * @param string $key Unique item identifier
     * @param mixed $closure Anonymous closure function
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
     * @param string $key Unique item identifier
     * @param int $value The amount by which to increment
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function increment($key, $value = 1)
    {
        // Check for key existence first as a temporary workaround
        // for this bug: https://github.com/krakjoe/apcu/issues/183
        if (apcu_exists($this->prefix($key))) {
            return apcu_inc($this->prefix($key), $value);
        }

        return false;
    }

    /**
     * Decrease the value of a stored integer.
     *
     * @param string $key Unique item identifier
     * @param int $value The amount by which to decrement
     *
     * @return mixed Item's new value on success, otherwise false
     */
    public function decrement($key, $value = 1)
    {
        // Check for key existence first as a temporary workaround
        // for this bug: https://github.com/krakjoe/apcu/issues/183
        if (apcu_exists($this->prefix($key))) {
            return apcu_dec($this->prefix($key), $value);
        }

        return false;
    }

    /**
     * Set a new expiration time for an item in the cache.
     *
     * @param string|array $key Unique item identifier
     * @param int $minutes Time in minutes until item expires
     *
     * @return bool True on success, otherwise false
     */
    public function touch($key, $minutes = 0)
    {
        if (is_array($key)) {
            return array_walk($key, function (string $key) use ($minutes) {
                $this->touch($key, $minutes);
            });
        }

        return $this->put($key, $this->get($key), $minutes);
    }

    /**
     * Removes an item from the cache.
     *
     * @param string|array $key Unique item identifier
     *
     * @return bool True on success, otherwise false
     */
    public function forget($key)
    {
        if (is_array($key)) {
            $keys = array_map(function (string $key): string {
                return $this->prefix($key);
            }, $key);

            return apcu_delete($keys) !== false;
        }

        return apcu_delete($key) !== false;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool True on success, otherwise false
     */
    public function flush()
    {
        return apcu_clear_cache();
    }

    /**
     * Set the cache prefix.
     *
     * @param string $prefix The cache prefix
     */
    protected function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Get prefixed key.
     *
     * @param string $key Unique item identifier
     *
     * @return string Prefixed unique identifier
     */
    protected function prefix($key)
    {
        return empty($this->prefix) ? $key : "{$this->prefix}:{$key}";
    }
}
