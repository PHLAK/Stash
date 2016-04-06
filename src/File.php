<?php

namespace Stash;

use Stash\Interfaces\Cacheable;

class File implements Cacheable {

    /** @var string Path to cache directory */
    protected $storagePath;

    /**
     * Stash\File constructor, runs on object creation
     *
     * @param string $storagePath Path to cache directory
     */
    public function __construct($storagePath) {
        $this->storagePath = $storagePath;
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
    public function put($key, $data, $minutes) {

        $cache = serialize([
            // TODO: Convert this to use DateTime
            'expires' => time() + ($minutes * 60),
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
        return $this->put($key, $data, PHP_INT_MAX - time() - 1);
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

        if (time() > $cache['expires']) return $default;

        return $cache['data'];

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
        return @unlink($this->cacheFile($key));
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
