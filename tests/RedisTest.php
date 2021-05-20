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

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);

        $this->assertFalse($this->stash->get('expired'));
    }
}
