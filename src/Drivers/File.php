<?php

namespace PHLAK\Stash\Drivers;

use PHLAK\Stash\Item;
use PHLAK\Stash\Interfaces\Cacheable;
use PHLAK\Stash\Exceptions\FileNotFoundException;
use RuntimeException;
use SplFileInfo;
use Closure;

class File implements Cacheable
{
    /** @var SplFileInfo The cache directory file path */
    protected $cacheDir;

    /**
     * Create a File cache driver object.
     *
     * @param \Closure|null $closure Anonymous configuration function
     *
     * @throws \RuntimeException
     */
    public function __construct(Closure $closure)
    {
        $closure = $closure->bindTo($this, self::class);

        $closure();

        if (empty($this->cacheDir)) {
            throw new RuntimeException('No cache directory defined');
        }
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
        $unlinked = array_map('unlink', glob($this->cacheDir . DIRECTORY_SEPARATOR . '*.cache.php'));

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
        return $this->cacheDir . DIRECTORY_SEPARATOR . sha1($key) . '.cache.php';
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
        $destination = $this->filePath($key);

        @mkdir(dirname($destination));

        return file_put_contents($destination, serialize(
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

    /**
     * Set the file cache directory path.
     *
     * @param string $path Path to cache directory
     *
     * @throws \PHLAK\Stash\Exceptions\FileNotFoundException
     * @throws \RuntimeException
     */
    protected function setCacheDir($path)
    {
        $cacheDir = new SplFileInfo($path);

        if (! $cacheDir->isDir()) {
            throw new FileNotFoundException("{$cacheDir} is not a directory or doesn't exists");
        }

        if (! $cacheDir->isWritable()) {
            throw new RuntimeException("{$cacheDir} is not writeable");
        }

        $this->cacheDir = $cacheDir;
    }
}
