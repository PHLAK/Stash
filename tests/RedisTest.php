<?php

namespace PHLAK\Stash\Tests;

use PHLAK\Stash;
use PHLAK\Stash\Tests\Traits\Cacheable;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class RedisTest extends TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp(): void
    {
        $this->stash = new Stash\Drivers\Redis(function ($redis) {
            $redis->pconnect('localhost', 6379);
        });
    }
}
