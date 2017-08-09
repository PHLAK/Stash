<?php

namespace Stash\Drivers;

use Stash\Item;

class File extends Driver
{
    /** @var string Path to cache directory */
    protected $storagePath;

    /**
     * Stash\File constructor, runs on object creation.
     *
     * @param string $storagePath Path to cache directory
     * @param string $prefix Key prefix for preventing collisions
     */
    public function __construct($storagePath, $prefix = '')
    {
        parent::__construct($prefix);

        $this->storagePath = rtrim($storagePath, DIRECTORY_SEPARATOR);
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
        return $this->putCacheContents($key, $data, $minutes);
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
     * @return mixed Cached data or false
     */
    public function get($key, $default = false)
    {
        $item = $this->getCacheContents($key);

        if ($item && $item->notexpired()) {
            return $item->data;
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
        $item = $this->getCacheContents($key);

        return $item && $item->notexpired();
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
        if (! $item = $this->getCacheContents($key)) {
            return false;
        }

        return $item->increment($value);
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
        return $this->increment($key, $value * -1);
    }

    /**
     * Set a new expiration time for an item in the cache.
     *
     * @param string  $key     Unique item identifier
     * @param int     $minutes Time in minutes until item expires
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
        return @unlink($this->filePath($key));
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool True on success, otherwise false
     */
    public function flush()
    {
        $unlinked = array_map('unlink', glob($this->storagePath . DIRECTORY_SEPARATOR . '*.cache'));

        return count(array_keys($unlinked, true)) == count($unlinked);
    }

    /**
     * Get an item's file path via it's key.
     *
     * @param string $key Unique item identifier
     *
     * @return string Path to cache item file
     */
    protected function filePath($key)
    {
        return $this->storagePath . DIRECTORY_SEPARATOR . sha1($this->prefix($key)) . '.cache';
    }

    /**
     * Put cache contents into a cache file.
     *
     * @param string $key     Unique item identifier
     * @param mixed  $data    Data to cache
     * @param int    $minutes Time in minutes until item expires
     *
     * @return mixed Cache file contents or false on failure
     */
    protected function putCacheContents($key, $data, $minutes)
    {
        return file_put_contents($this->filePath($key), serialize(
            new Item($data, $minutes)
        ), LOCK_EX) ? true : false;
    }

    /**
     * Retrieve the contents of a cache file.
     *
     * @param string $key Unique item identifier
     *
     * @return mixed Cache file contents or false on failure
     */
    protected function getCacheContents($key)
    {
        $item = unserialize(@file_get_contents($this->filePath($key)));

        if (! $item || $item->expired()) {
            return false;
        }

        return $item;
    }
}
