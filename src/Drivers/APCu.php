<?php

namespace PHLAK\Stash\Drivers;

use Closure;
use PHLAK\Stash\Interfaces\Cacheable;
use RuntimeException;

class APCu implements Cacheable
{
    /** @var string Prefix string to prevent collisions */
    private $prefix = '';

    /**
     * Create an APCu cache driver object.
     *
     * @param Closure|null $closure Anonymous configuration function
     */
    public function __construct(?Closure $closure = null)
    {
        if (is_callable($closure)) {
            $closure = $closure->bindTo($this, self::class);

            if (! $closure) {
                throw new RuntimeException('Failed to bind closure');
            }

            $closure();
        }
    }

    public function put(string $key, mixed $data, int $ttl = 0): bool
    {
        return apcu_store($this->prefix($key), $data, $ttl);
    }

    public function forever(string $key, mixed $data): bool
    {
        return $this->put($key, $data);
    }

    public function get(string $key, mixed $default = false): mixed
    {
        return apcu_fetch($this->prefix($key)) ?: $default;
    }

    public function has(string $key): bool
    {
        return apcu_exists($this->prefix($key));
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
        // Check for key existence first as a temporary workaround
        // for this bug: https://github.com/krakjoe/apcu/issues/183
        if (apcu_exists($this->prefix($key))) {
            return apcu_inc($this->prefix($key), $value);
        }

        return false;
    }

    public function decrement(string $key, int $value = 1): mixed
    {
        // Check for key existence first as a temporary workaround
        // for this bug: https://github.com/krakjoe/apcu/issues/183
        if (apcu_exists($this->prefix($key))) {
            return apcu_dec($this->prefix($key), $value);
        }

        return false;
    }

    public function touch(array|string $key, int $ttl = 0): bool
    {
        if (is_array($key)) {
            return array_walk($key, function (string $key) use ($ttl): void {
                $this->touch($key, $ttl);
            });
        }

        return $this->put($key, $this->get($key), $ttl);
    }

    public function forget(array|string $key): bool
    {
        if (is_array($key)) {
            $keys = array_map(function (string $key): string {
                return $this->prefix($key);
            }, $key);

            return apcu_delete($keys) !== false;
        }

        return apcu_delete($key) !== false;
    }

    public function flush(): bool
    {
        return apcu_clear_cache();
    }

    /** Set the cache prefix. */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /** Get prefixed key. */
    private function prefix(string $key): string
    {
        return empty($this->prefix) ? $key : "{$this->prefix}:{$key}";
    }
}
