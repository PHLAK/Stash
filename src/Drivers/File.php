<?php

namespace Stash\Drivers;

use Stash\Interfaces\Cacheable;
use Carbon\Carbon;

class File implements Cacheable {

    /** @var string Path to cache directory */
    protected $storagePath;

    /**
     * Stash\File constructor, runs on object creation
     *
     * @param string $storagePath Path to cache directory
     */
    public function __construct($storagePath) {
        $this->storagePath = rtrim($storagePath, DIRECTORY_SEPARATOR);
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

        $cache = serialize([
            'expires' => $minutes > 0 ? Carbon::now()->addMinutes($minutes) : Carbon::maxValue(),
            'data'    => $data
        ]);

        return file_put_contents($this->cacheFile($key), $cache, LOCK_EX) ? true : false;

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

        $contents = @file_get_contents($this->cacheFile($key));

        if ($contents === false) return $default;

        $cache = unserialize($contents);

        return Carbon::now()->lte($cache['expires']) ? $cache['data'] : $default;

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
        return $this->storagePath . DIRECTORY_SEPARATOR . sha1($key) . '.cache';
    }

}
