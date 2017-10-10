<?php

use PHLAK\Stash;

class ItemTest extends PHPUnit_Framework_TestCase
{
    protected $item;

    public function setUp()
    {
        $this->item = new Stash\Item('Test data');
    }

    public function test_it_can_contain_a_string()
    {
        $item = new Stash\Item('Some string');

        $this->assertEquals('Some string', $item->data);
    }

    public function test_it_can_contain_an_integer()
    {
        $item = new Stash\Item(1337);

        $this->assertEquals(1337, $item->data);
    }

    public function test_it_can_contain_an_array()
    {
        $item = new Stash\Item(['alice', 1337, false]);

        $this->assertEquals(['alice', 1337, false], $item->data);
    }

    public function test_it_can_contain_booleans()
    {
        $trueItem = new Stash\Item(true);
        $falseItem = new Stash\Item(false);

        $this->assertTrue($trueItem->data);
        $this->assertFalse($falseItem->data);
    }

    public function test_it_can_contain_an_object()
    {
        $class = new stdClass;
        $class->boolean = true;

        $item = new Stash\Item($class);

        $this->assertTrue($item->data->boolean);
    }

    public function test_it_has_an_expiration()
    {
        $this->assertInstanceOf('Carbon\Carbon', $this->item->expires);
    }

    public function test_it_isnt_expired()
    {
        $this->assertFalse($this->item->expired());
        $this->assertTrue($this->item->notExpired());
    }

    public function test_it_can_expire()
    {
        $item = new Stash\Item('Test data', -1);

        $this->assertTrue($item->expired());
        $this->assertFalse($item->notExpired());
    }

    public function test_it_can_be_incremented()
    {
        $item = new Stash\Item(1336);

        $this->assertEquals(1337, $item->increment());
        $this->assertEquals(2000, $item->increment(663));
    }

    public function test_it_can_be_decremented()
    {
        $item = new Stash\Item(1338);

        $this->assertEquals(1337, $item->decrement());
        $this->assertEquals(1000, $item->decrement(337));
    }

    public function test_it_returns_false_when_incrementing_a_non_integer()
    {
        $this->assertFalse($this->item->increment());
    }

    public function test_it_returns_false_when_decrementing_a_non_integer()
    {
        $this->assertFalse($this->item->decrement());
    }
}
