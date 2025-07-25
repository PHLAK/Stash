<?php

namespace Tests\Drivers;

use PHLAK\Stash\Drivers\Ephemeral;
use PHLAK\Stash\Interfaces\Cacheable;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Ephemeral::class)]
class EphemeralTest extends TestCase
{
    use CacheableTrait;

    protected Cacheable $stash;

    public function setUp(): void
    {
        $this->stash = new Ephemeral;
    }
}
