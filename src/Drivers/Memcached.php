<?php

namespace PHLAK\Stash\Drivers;

use Closure;
use Memcached as PhpMemcached;
use PHLAK\Stash\Interfaces\Cacheable;

class Memcached implements Cacheable
{
    /** @var PhpMemcached Instance of Memcached */
    private PhpMemcached $memcached;

    /**
     * Create a Memcached cache driver object.
     *
     * @param Closure $closure Anonymous configuration function
     */
    public function __construct(Closure $closure)
    {
        $this->memcached = new PhpMemcached;

        $closure($this->memcached);
    }

    public function put(string $key, mixed $data, int $ttl = 0): bool
    {
        $expiration = $ttl === 0 ? 0 : time() + $ttl;

        return $this->memcached->set($key, $data, $expiration);
    }

    public function forever(string $key, mixed $data): bool
    {
        return $this->put($key, $data);
    }

    public function get(string $key, mixed $default = false): mixed
    {
        return $this->memcached->get($key) ?: $default;
    }

    public function has(string $key): bool
    {
        $this->memcached->get($key);

        return $this->memcached->getResultCode() == PhpMemcached::RES_SUCCESS;
    }

    public function remember(string $key, int $ttl, Closure $closure): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $data = $closure();

        return $this->put($key, $data, $ttl) ? $data : false;
    }

    public function rememberForever(string $key, Closure $closure): mixed
    {
        return $this->remember($key, 0, $closure);
    }

    public function increment(string $key, int $value = 1): mixed
    {
        return $this->memcached->increment($key, $value);
    }

    public function decrement(string $key, int $value = 1): mixed
    {
        return $this->memcached->decrement($key, $value);
    }

    public function touch(array|string $key, int $ttl = 0): bool
    {
        if (is_array($key)) {
            return array_walk($key, function (string $key) use ($ttl) {
                $this->touch($key, $ttl);
            });
        }

        if (! $this->has($key)) {
            return $this->put($key, false);
        }

        return $this->memcached->touch($key, $ttl);
    }

    public function forget(array|string $key): bool
    {
        if (is_array($key)) {
            return array_reduce($this->memcached->deleteMulti($key), function (bool $carry, string $item) {
                return $carry && $item;
            }, true);
        }

        return $this->memcached->delete($key);
    }

    public function flush(): bool
    {
        return $this->memcached->flush();
    }
}
