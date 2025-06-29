<?php

namespace PHLAK\Stash\Tests;

use PHLAK\Stash;
use PHLAK\Stash\Interfaces\Cacheable;
use PHLAK\Stash\Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class MemcachedTest extends TestCase
{
    use CacheableTrait;

    protected Cacheable $stash;

    public function setUp(): void
    {
        $this->stash = new Stash\Drivers\Memcached(function ($memcached) {
            $memcached->addServer('localhost', 11211);
        });
    }
}
