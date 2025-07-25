<?php

namespace Tests;

use Memcached;
use PHLAK\Stash;
use PHLAK\Stash\Cache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Redis;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Cache::class)]
class CacheTest extends TestCase
{
    #[Test]
    public function it_can_instantiate_the_apc_driver(): void
    {
        $apc = Stash\Cache::apcu();

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $apc);
        $this->assertInstanceOf(Stash\Drivers\APCu::class, $apc);
    }

    #[Test]
    public function it_can_instantiate_the_apc_driver_with_prefix(): void
    {
        $apc = Stash\Cache::apcu(function (): void {
            $this->setPrefix('stash_test');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $apc);
        $this->assertInstanceOf(Stash\Drivers\APCu::class, $apc);
    }

    #[Test]
    public function it_can_instantiate_the_file_driver(): void
    {
        $file = Stash\Cache::file(function (): void {
            $this->setCacheDir(__DIR__ . '/cache');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $file);
        $this->assertInstanceOf(Stash\Drivers\File::class, $file);
    }

    #[Test]
    public function it_can_instantiate_the_file_driver_with_prefix(): void
    {
        $file = Stash\Cache::file(function (): void {
            $this->setCacheDir(__DIR__ . '/cache');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $file);
        $this->assertInstanceOf(Stash\Drivers\File::class, $file);
    }

    #[Test]
    public function it_can_instantiate_the_memcached_driver(): void
    {
        $memcached = Stash\Cache::memcached(function ($memcached) {
            $memcached->addServer('localhost', 11211);
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $memcached);
        $this->assertInstanceOf(Stash\Drivers\Memcached::class, $memcached);
    }

    #[Test]
    public function it_can_instantiate_the_memcached_driver_with_prefix(): void
    {
        $memcached = Stash\Cache::memcached(function ($memcached) {
            $memcached->addServer('localhost', 11211);
            $memcached->setOption(Memcached::OPT_PREFIX_KEY, 'stash_test');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $memcached);
        $this->assertInstanceOf(Stash\Drivers\Memcached::class, $memcached);
    }

    #[Test]
    public function it_can_instantiate_the_redis_driver(): void
    {
        $redis = Stash\Cache::redis(function ($redis) {
            $redis->pconnect('localhost', 6379);
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $redis);
        $this->assertInstanceOf(Stash\Drivers\Redis::class, $redis);
    }

    #[Test]
    public function it_can_instantiate_the_redis_driver_with_prefix(): void
    {
        $redis = Stash\Cache::redis(function ($redis) {
            $redis->pconnect('localhost', 6379);
            $redis->setOption(Redis::OPT_PREFIX, 'stash_test');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $redis);
        $this->assertInstanceOf(Stash\Drivers\Redis::class, $redis);
    }

    #[Test]
    public function it_can_instantiate_the_ephemeral_driver_via_the_named_constructor(): void
    {
        $ephemeral = Stash\Cache::ephemeral();

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $ephemeral);
        $this->assertInstanceOf(Stash\Drivers\Ephemeral::class, $ephemeral);
    }
}
