<?php

namespace PHLAK\Stash\Drivers;

use Closure;
use PHLAK\Stash\Interfaces\Cacheable;
use PHLAK\Stash\Item;

class Ephemeral implements Cacheable
{
    /** @var array<string, Item> Array of cached items */
    private array $cache = [];

    public function put(string $key, mixed $data, int $ttl = 0): bool
    {
        $this->cache[$key] = new Item($data, $ttl);

        return true;
    }

    public function forever(string $key, mixed $data): bool
    {
        return $this->put($key, $data);
    }

    public function get(string $key, mixed $default = false): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            /** @var Item $item */
            $item = $this->cache[$key];

            if ($item->notExpired()) {
                return $item->data;
            }
        }

        return $default;
    }

    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->cache)) {
            $item = $this->cache[$key];

            return $item->notExpired();
        }

        return false;
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
        if (array_key_exists($key, $this->cache)) {
            $item = $this->cache[$key];

            return $item->increment($value);
        }

        return false;
    }

    public function decrement(string $key, int $value = 1): mixed
    {
        return $this->increment($key, $value * -1);
    }

    public function touch(array|string $key, int $ttl = 0): bool
    {
        if (is_array($key)) {
            return array_walk($key, function (string $key) use ($ttl) {
                $this->touch($key, $ttl);
            });
        }

        return $this->put($key, $this->get($key), $ttl);
    }

    public function forget(array|string $key): bool
    {
        if (is_array($key)) {
            return array_reduce($key, function (bool $carry, string $key) {
                return $carry && $this->forget($key);
            }, true);
        }

        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);

            return true;
        }

        return false;
    }

    public function flush(): bool
    {
        $this->cache = [];

        return true;
    }
}
