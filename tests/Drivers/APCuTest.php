<?php

namespace Tests\Drivers;

use PHLAK\Stash\Drivers\APCu;
use PHLAK\Stash\Interfaces\Cacheable;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Cacheable::class), CoversClass(APCu::class)]
class APCuTest extends TestCase
{
    use CacheableTrait;

    protected Cacheable $stash;

    public function setUp(): void
    {
        $this->stash = new APCu;
    }
}
