<?php

namespace Tests\Drivers;

use PHLAK\Stash\Drivers\APCu;
use PHLAK\Stash\Interfaces\Cacheable;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(APCu::class)]
class APCuTest extends TestCase
{
    use CacheableTrait;

    protected Cacheable $stash;

    public function setUp(): void
    {
        if (! extension_loaded('apcu') || ! ini_get('apc.enabled')) {
            $this->markTestSkipped('APCu extension is not loaded or enabled');
        }

        $this->stash = new APCu;
    }
}
