<?php

namespace PHLAK\Stash\Drivers;

use Closure;
use PHLAK\Stash\Exceptions\FileNotFoundException;
use PHLAK\Stash\Interfaces\Cacheable;
use PHLAK\Stash\Item;
use RuntimeException;
use SplFileInfo;

class File implements Cacheable
{
    /** @var SplFileInfo The cache directory file path */
    private SplFileInfo $cacheDir;

    /**
     * Create a File cache driver object.
     *
     * @param Closure $closure Anonymous configuration function
     *
     * @throws RuntimeException
     */
    public function __construct(Closure $closure)
    {
        $closure = $closure->bindTo($this, self::class);

        if (! $closure) {
            throw new RuntimeException('Failed to bind closure');
        }

        $closure();

        if (! isset($this->cacheDir) || empty($this->cacheDir)) {
            throw new RuntimeException('No cache directory defined');
        }
    }

    public function put(string $key, mixed $data, int $minutes = 0): bool
    {
        return $this->putCacheContents($key, $data, $minutes);
    }

    public function forever(string $key, mixed $data): bool
    {
        return $this->put($key, $data);
    }

    public function get(string $key, mixed $default = false): mixed
    {
        $item = $this->getCacheContents($key);

        if ($item && $item->notexpired()) {
            return $item->data;
        }

        return $default;
    }

    public function has(string $key): bool
    {
        $item = $this->getCacheContents($key);

        return $item && $item->notexpired();
    }

    public function remember(string $key, int $minutes, Closure $closure): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $data = $closure();

        return $this->put($key, $data, $minutes) ? $data : false;
    }

    public function rememberForever(string $key, Closure $closure): mixed
    {
        return $this->remember($key, 0, $closure);
    }

    public function increment(string $key, int $value = 1): mixed
    {
        if (! $item = $this->getCacheContents($key)) {
            return false;
        }

        return $item->increment($value);
    }

    public function decrement(string $key, int $value = 1): mixed
    {
        return $this->increment($key, $value * -1);
    }

    public function touch(array|string $key, int $minutes = 0): bool
    {
        if (is_array($key)) {
            return array_walk($key, function (string $key) use ($minutes) {
                $this->touch($key, $minutes);
            });
        }

        return $this->put($key, $this->get($key), $minutes);
    }

    public function forget(array|string $key): bool
    {
        if (is_array($key)) {
            return array_reduce($key, function (bool $carry, string $key) {
                return $carry && $this->forget($key);
            }, true);
        }

        return @unlink($this->filePath($key));
    }

    public function flush(): bool
    {
        $unlinked = array_map('unlink', glob($this->cacheDir . DIRECTORY_SEPARATOR . '*.cache.php'));

        return count(array_keys($unlinked, true)) == count($unlinked);
    }

    /** Get an item's file path via it's key. */
    private function filePath(string $key): string
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . sha1($key) . '.cache.php';
    }

    /** Put cache contents into a cache file. */
    private function putCacheContents(string $key, mixed $data, int $minutes): bool
    {
        $destination = $this->filePath($key);

        @mkdir(dirname($destination));

        return file_put_contents($destination, serialize(
            new Item($data, $minutes)
        ), LOCK_EX) ? true : false;
    }

    /** Retrieve the contents of a cache file. */
    private function getCacheContents(string $key): mixed
    {
        $item = unserialize(@file_get_contents($this->filePath($key)));

        if (! $item || $item->expired()) {
            return false;
        }

        return $item;
    }

    /** Set the file cache directory path. */
    private function setCacheDir(string $path): void
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
