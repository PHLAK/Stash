<?php

namespace PHLAK\Stash\Tests;

use PHLAK\Stash;
use PHLAK\Stash\Interfaces\Cacheable;
use PHLAK\Stash\Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class RedisTest extends TestCase
{
    use CacheableTrait;

    protected Cacheable $stash;

    public function setUp(): void
    {
        $this->stash = new Stash\Drivers\Redis(function ($redis) {
            $redis->pconnect('localhost', 6379);
        });
    }
}
