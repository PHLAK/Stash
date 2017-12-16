<?php

use PHLAK\Stash;

class CacheTest extends PHPUnit_Framework_TestCase
{
    public function test_it_can_instantiate_the_apc_driver()
    {
        $apc = Stash\Cache::make('apcu');

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $apc);
        $this->assertInstanceOf(Stash\Drivers\APCu::class, $apc);
    }

    public function test_it_can_instantiate_the_apc_driver_with_prefix()
    {
        $apc = Stash\Cache::make('apcu', function () {
            return ['prefix' => 'stash_test'];
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $apc);
        $this->assertInstanceOf(Stash\Drivers\APCu::class, $apc);
    }

    public function test_it_can_instantiate_the_file_driver()
    {
        $file = Stash\Cache::make('file', function () {
            return ['dir' => __DIR__ . '/cache'];
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $file);
        $this->assertInstanceOf(Stash\Drivers\File::class, $file);
    }

    public function test_it_can_instantiate_the_file_driver_with_prefix()
    {
        $file = Stash\Cache::make('file', function () {
            return [
                'dir' => __DIR__ . '/cache',
                'prefix' => 'stash_test'
            ];
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $file);
        $this->assertInstanceOf(Stash\Drivers\File::class, $file);
    }

    public function test_it_can_instantiate_the_memcached_driver()
    {
        $memcached = Stash\Cache::make('memcached', function ($memcached) {
            $memcached->addServer('localhost', 11211);

            return $memcached;
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $memcached);
        $this->assertInstanceOf(Stash\Drivers\Memcached::class, $memcached);
    }

    public function test_it_can_instantiate_the_memcached_driver_with_prefix()
    {
        $memcached = Stash\Cache::make('memcached', function ($memcached) {
            $memcached->addServer('localhost', 11211);
            $memcached->setOption(\Memcached::OPT_PREFIX_KEY, 'stash_test');

            return $memcached;
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $memcached);
        $this->assertInstanceOf(Stash\Drivers\Memcached::class, $memcached);
    }

    public function test_it_can_instantiate_the_redis_driver()
    {
        $redis = Stash\Cache::make('redis', function ($redis) {
            $redis->pconnect('localhost', 6379);

            return $redis;
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $redis);
        $this->assertInstanceOf(Stash\Drivers\Redis::class, $redis);
    }

    public function test_it_can_instantiate_the_redis_driver_with_prefix()
    {
        $redis = Stash\Cache::make('redis', function ($redis) {
            $redis->pconnect('localhost', 6379);

            $redis->setOption(Redis::OPT_PREFIX, 'stash_test');

            return $redis;
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $redis);
        $this->assertInstanceOf(Stash\Drivers\Redis::class, $redis);
    }

    public function test_it_can_instantiate_the_ephemeral_driver()
    {
        $ephemeral = Stash\Cache::make('ephemeral');

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $ephemeral);
        $this->assertInstanceOf(Stash\Drivers\Ephemeral::class, $ephemeral);
    }

    public function test_it_can_instantiate_the_ephemeral_driver_with_prefix()
    {
        $ephemeral = Stash\Cache::make('ephemeral', function () {
            return ['prefix' => 'stash_test'];
        });

        $this->assertInstanceOf(Stash\Interfaces\Cacheable::class, $ephemeral);
        $this->assertInstanceOf(Stash\Drivers\Ephemeral::class, $ephemeral);
    }

    public function test_it_throws_an_exception_for_an_invalid_driver()
    {
        $this->setExpectedException(Stash\Exceptions\InvalidDriverException::class);

        $file = Stash\Cache::make('snozberries');
    }
}
