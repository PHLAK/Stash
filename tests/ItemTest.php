<?php

namespace Tests;

use Carbon\CarbonInterface;
use PHLAK\Stash;
use PHLAK\Stash\Interfaces\Cacheable;
use PHLAK\Stash\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use stdClass;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Cacheable::class), CoversClass(Item::class)]
class ItemTest extends TestCase
{
    #[Test]
    public function it_can_contain_a_string(): void
    {
        $item = new Stash\Item('Some string');

        $this->assertEquals('Some string', $item->data);
    }

    #[Test]
    public function it_can_contain_an_integer(): void
    {
        $item = new Stash\Item(1337);

        $this->assertEquals(1337, $item->data);
    }

    #[Test]
    public function it_can_contain_an_array(): void
    {
        $item = new Stash\Item(['alice', 1337, false]);

        $this->assertEquals(['alice', 1337, false], $item->data);
    }

    #[Test]
    public function it_can_contain_booleans(): void
    {
        $trueItem = new Stash\Item(true);
        $falseItem = new Stash\Item(false);

        $this->assertTrue($trueItem->data);
        $this->assertFalse($falseItem->data);
    }

    #[Test]
    public function it_can_contain_an_object(): void
    {
        $class = new stdClass;
        $class->boolean = true;

        $item = new Stash\Item($class);

        $this->assertTrue($item->data->boolean);
    }

    #[Test]
    public function it_has_an_expiration(): void
    {
        $item = new Stash\Item('Test data');

        $this->assertInstanceOf(CarbonInterface::class, $item->expires);
    }

    #[Test]
    public function it_isnt_expired(): void
    {
        $item = new Stash\Item('Test data');

        $this->assertFalse($item->expired());
        $this->assertTrue($item->notExpired());
    }

    #[Test]
    public function it_can_expire(): void
    {
        $item = new Stash\Item('Test data', -1);

        $this->assertTrue($item->expired());
        $this->assertFalse($item->notExpired());
    }

    #[Test]
    public function it_can_be_incremented(): void
    {
        $item = new Stash\Item(1336);

        $this->assertEquals(1337, $item->increment());
        $this->assertEquals(2000, $item->increment(663));
    }

    #[Test]
    public function it_can_be_decremented(): void
    {
        $item = new Stash\Item(1338);

        $this->assertEquals(1337, $item->decrement());
        $this->assertEquals(1000, $item->decrement(337));
    }

    #[Test]
    public function it_returns_false_when_incrementing_a_non_integer(): void
    {
        $item = new Stash\Item('Test data');

        $this->assertFalse($item->increment());
    }

    #[Test]
    public function it_returns_false_when_decrementing_a_non_integer(): void
    {
        $item = new Stash\Item('Test data');

        $this->assertFalse($item->decrement());
    }
}
