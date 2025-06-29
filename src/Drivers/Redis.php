<?php

namespace PHLAK\Stash\Drivers;

use Closure;
use PHLAK\Stash\Interfaces\Cacheable;
use Redis as PhpRedis;

class Redis implements Cacheable
{
    /** @var PhpRedis Instance of Redis */
    private PhpRedis $redis;

    /**
     * Create a Redis cache driver object.
     *
     * @param Closure $closure Anonymous configuration function
     */
    public function __construct(Closure $closure)
    {
        $this->redis = new PhpRedis;

        $closure($this->redis);
    }

    public function put(string $key, mixed $data, int $minutes = 0): bool
    {
        $expiration = $minutes == 0 ? null : $minutes * 60;

        if ($minutes < 0) {
            return true;
        }

        return $this->redis->set($key, serialize($data), $expiration);
    }

    public function forever(string $key, mixed $data): bool
    {
        return $this->put($key, $data);
    }

    public function get(string $key, mixed $default = false): mixed
    {
        if ($data = $this->redis->get($key)) {
            return unserialize($data);
        }

        return $default;
    }

    public function has(string $key): bool
    {
        return (bool) $this->redis->exists($key);
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
        $data = $this->get($key);

        if (is_int($data)) {
            $ttl = $this->redis->ttl($key);

            $this->put($key, $data += $value, $ttl == -1 ? 0 : $ttl);

            return $data;
        }

        return false;
    }

    public function decrement(string $key, int $value = 1): mixed
    {
        $data = $this->get($key);

        if (is_int($data)) {
            $ttl = $this->redis->ttl($key);

            $this->put($key, $data -= $value, $ttl == -1 ? 0 : $ttl);

            return $data;
        }

        return false;
    }

    public function touch(array|string $key, int $minutes = 0): bool
    {
        if (is_array($key)) {
            return array_walk($key, function (string $key) use ($minutes) {
                $this->touch($key, $minutes);
            });
        }

        if (! $this->has($key)) {
            return $this->put($key, false);
        }

        return $this->redis->expire($key, $minutes * 60);
    }

    public function forget(array|string $key): bool
    {
        return (bool) $this->redis->del($key);
    }

    public function flush(): bool
    {
        return $this->redis->flushDb();
    }
}
