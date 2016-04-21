<?php

namespace Stash\Drivers;

use Carbon\Carbon;

class File extends Driver {

    /** @var string Path to cache directory */
    protected $storagePath;

    /**
     * Stash\File constructor, runs on object creation
     *
     * @param string $storagePath Path to cache directory
     */
    public function __construct($storagePath, $prefix = '') {
        $this->storagePath = rtrim($storagePath, DIRECTORY_SEPARATOR);
        $this->prefix      = $prefix;
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
        $expires = $minutes > 0 ? Carbon::now()->addMinutes($minutes) : Carbon::maxValue();
        return $this->putCacheContents($key, $data, $expires);
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
     * @return mixed           Cached data or false
     */
    public function get($key, $default = false) {

        if ($cache = $this->getCacheContents($key)) {
            if (Carbon::now()->lte($cache['expires'])) return $cache['data'];
        }

        return $default;

    }

    /**
     * Check if an item exists in the cache
     *
     * @param  string $key Unique item identifier
     *
     * @return bool        True if item exists, otherwise false
     */
    public function has($key) {
        return $this->get($key) ? true : false;
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
     * Increase the value of a stored integer
     *
     * @param  string $key   Unique item identifier
     * @param  int    $value The ammount by which to increment
     *
     * @return mixed         Item's new value on success, otherwise false
     */
    public function increment($key, $value = 1) {

        if (! $cache = $this->getCacheContents($key)) return false;

        if (Carbon::now()->lte($cache['expires']) && is_int($cache['data'])) {
            $newData = $cache['data'] + $value;
            if ($this->putCacheContents($key, $newData, $cache['expires'])) return $newData;
        }

        return false;

    }

    /**
     * Decrease the value of a stored integer
     *
     * @param  string $key   Unique item identifier
     * @param  int    $value The ammount by which to decrement
     *
     * @return mixed         Item's new value on success, otherwise false
     */
    public function decrement($key, $value = 1) {

        if (! $cache = $this->getCacheContents($key)) return false;

        if (Carbon::now()->lte($cache['expires']) && is_int($cache['data'])) {
            $newData = $cache['data'] - $value;
            if ($this->putCacheContents($key, $newData, $cache['expires'])) return $newData;
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
        return @unlink($this->cacheFile($key));
    }

    /**
     * Remove all items from the cache
     *
     * @return bool True on success, otherwise false
     */
    public function flush() {
        $unlinked = array_map('unlink', glob($this->storagePath . DIRECTORY_SEPARATOR . '*.cache'));
        return count(array_keys($unlinked, true)) == count($unlinked);
    }

    /**
     * Get file name for cache item by the provided key
     *
     * @param  string $key Uniqe item identifier
     *
     * @return string      Path to cache item file
     */
    protected function cacheFile($key) {
        return $this->storagePath . DIRECTORY_SEPARATOR . sha1($this->prefix($key)) . '.cache';
    }

    /**
     * Put cache contents into a cache file
     *
     * @param  string $key     Unique item identifier
     * @param  mixed  $data    Data to cache
     * @param  Carbon $expires Carbon instance representing the expiration time
     *
     * @return mixed       Cache file contents or false on failure
     */
    protected function putCacheContents($key, $data, Carbon $expires) {
        return file_put_contents($this->cacheFile($key), serialize([
            'expires' => $expires,
            'data'    => $data
        ]), LOCK_EX) ? true : false;
    }

    /**
     * Retreive the contents of a cache file
     *
     * @param  string $key Unique item identifier
     *
     * @return mixed       Cache file contents or false on failure
     */
    protected function getCacheContents($key) {
        $contents = @file_get_contents($this->cacheFile($key));
        if ($contents === false) return false;
        return unserialize($contents);
    }

}
