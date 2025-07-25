<?php

namespace Tests\Helpers;

use PHLAK\Stash\Helpers\TTL;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TTL::class)]
class TTLTest extends TestCase
{
    #[Test]
    public function it_can_get_the_ttl_in_seconds(): void
    {
        $ttl = TTL::seconds(42);

        $this->assertSame(42, $ttl);
    }

    #[Test]
    public function it_can_get_the_ttl_in_minutes(): void
    {
        $ttl = TTL::minutes(42);

        $this->assertSame(2520, $ttl);
    }

    #[Test]
    public function it_can_get_the_ttl_in_hours(): void
    {
        $ttl = TTL::hours(42);

        $this->assertSame(151200, $ttl);
    }

    #[Test]
    public function it_can_get_the_ttl_in_days(): void
    {
        $ttl = TTL::days(42);

        $this->assertSame(3628800, $ttl);
    }
}
