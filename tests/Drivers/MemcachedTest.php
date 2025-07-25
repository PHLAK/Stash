<?php

namespace Tests\Drivers;

use PHLAK\Stash\Drivers\Memcached;
use PHLAK\Stash\Interfaces\Cacheable;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Memcached::class)]
class MemcachedTest extends TestCase
{
    use CacheableTrait;

    protected Cacheable $stash;

    public function setUp(): void
    {
        if (! class_exists('Memcached')) {
            $this->markTestSkipped('Memcached extension is not available');
        }

        $this->stash = new Memcached(function ($memcached): void {
            $memcached->addServer('localhost', 11211);
        });
    }
}
