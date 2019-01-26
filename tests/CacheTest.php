<?php

namespace PHLAK\Stash\Tests;

use PHPUnit\Framework\TestCase;
use PHLAK\Stash;
use Memcached;
use Redis;

class CacheTest extends TestCase
{
    public function test_it_can_instantiate_the_apc_driver()
    {
        $apc = Stash\Cache::apcu();

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $apc);
        $this->assertInstanceOf(Stash\Drivers\APCu::class, $apc);
    }

    public function test_it_can_instantiate_the_apc_driver_with_prefix()
    {
        $apc = Stash\Cache::apcu(function () {
            $this->setPrefix('stash_test');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $apc);
        $this->assertInstanceOf(Stash\Drivers\APCu::class, $apc);
    }

    public function test_it_can_instantiate_the_apc_driver_with_the_make_method()
    {
        $apc = @Stash\Cache::make('apcu');

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $apc);
        $this->assertInstanceOf(Stash\Drivers\APCu::class, $apc);
    }

    public function test_it_can_instantiate_the_file_driver()
    {
        $file = Stash\Cache::file(function () {
            $this->setCacheDir(__DIR__ . '/cache');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $file);
        $this->assertInstanceOf(Stash\Drivers\File::class, $file);
    }

    public function test_it_can_instantiate_the_file_driver_with_prefix()
    {
        $file = Stash\Cache::file(function () {
            $this->setCacheDir(__DIR__ . '/cache');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $file);
        $this->assertInstanceOf(Stash\Drivers\File::class, $file);
    }

    public function test_it_can_instantiate_the_file_driver_with_the_make_method()
    {
        $file = @Stash\Cache::make('file', function () {
            $this->setCacheDir(__DIR__ . '/cache');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $file);
        $this->assertInstanceOf(Stash\Drivers\File::class, $file);
    }

    public function test_it_can_instantiate_the_memcached_driver()
    {
        $memcached = Stash\Cache::memcached(function ($memcached) {
            $memcached->addServer('localhost', 11211);
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $memcached);
        $this->assertInstanceOf(Stash\Drivers\Memcached::class, $memcached);
    }

    public function test_it_can_instantiate_the_memcached_driver_with_prefix()
    {
        $memcached = Stash\Cache::memcached(function ($memcached) {
            $memcached->addServer('localhost', 11211);
            $memcached->setOption(Memcached::OPT_PREFIX_KEY, 'stash_test');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $memcached);
        $this->assertInstanceOf(Stash\Drivers\Memcached::class, $memcached);
    }

    public function test_it_can_instantiate_the_memcached_driver_with_the_make_method()
    {
        $memcached = @Stash\Cache::make('memcached', function ($memcached) {
            $memcached->addServer('localhost', 11211);
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $memcached);
        $this->assertInstanceOf(Stash\Drivers\Memcached::class, $memcached);
    }

    public function test_it_can_instantiate_the_redis_driver()
    {
        $redis = Stash\Cache::redis(function ($redis) {
            $redis->pconnect('localhost', 6379);
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $redis);
        $this->assertInstanceOf(Stash\Drivers\Redis::class, $redis);
    }

    public function test_it_can_instantiate_the_redis_driver_with_prefix()
    {
        $redis = Stash\Cache::redis(function ($redis) {
            $redis->pconnect('localhost', 6379);
            $redis->setOption(Redis::OPT_PREFIX, 'stash_test');
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $redis);
        $this->assertInstanceOf(Stash\Drivers\Redis::class, $redis);
    }

    public function test_it_can_instantiate_the_redis_driver_with_the_make_method()
    {
        $redis = @Stash\Cache::make('redis', function ($redis) {
            $redis->pconnect('localhost', 6379);
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $redis);
        $this->assertInstanceOf(Stash\Drivers\Redis::class, $redis);
    }

    public function test_it_can_instantiate_the_ephemeral_driver_via_the_named_constructor()
    {
        $ephemeral = Stash\Cache::ephemeral();

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $ephemeral);
        $this->assertInstanceOf(Stash\Drivers\Ephemeral::class, $ephemeral);
    }

    public function test_it_can_instantiate_the_ephemeral_driver_with_the_make_method()
    {
        $ephemeral = @Stash\Cache::make('ephemeral');

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $ephemeral);
        $this->assertInstanceOf(Stash\Drivers\Ephemeral::class, $ephemeral);
    }

    public function test_it_throws_an_exception_when_instantiatoing_an_invalid_driver_via_the_make_method()
    {
        $this->expectException(Stash\Exceptions\InvalidDriverException::class);

        $file = @Stash\Cache::make('snozberries');
    }
}
