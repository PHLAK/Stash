<?php

namespace Tests\Drivers;

use PHLAK\Stash\Drivers\Redis;
use PHLAK\Stash\Interfaces\Cacheable;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Redis::class)]
class RedisTest extends TestCase
{
    use CacheableTrait;

    protected Cacheable $stash;

    public function setUp(): void
    {
        if (! class_exists('Redis')) {
            $this->markTestSkipped('Redis extension is not available');
        }

        $this->stash = new Redis(function ($redis): void {
            $redis->pconnect('localhost', 6379);
        });
    }
}
