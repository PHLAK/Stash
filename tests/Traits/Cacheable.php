<?php

namespace Tests\Traits;

use PHLAK\Stash\Helpers\TTL;
use PHLAK\Stash\Interfaces\Cacheable as CacheableInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(CacheableInterface::class)]
trait Cacheable
{
    public function tearDown(): void
    {
        $this->stash->flush();
    }

    #[Test]
    public function it_can_add_and_retrieve_an_item(): void
    {
        $this->assertTrue($this->stash->put('put', 'jabberwocky', TTL::minutes(5)));
        $this->assertEquals('jabberwocky', $this->stash->get('put'));
    }

    #[Test]
    public function it_can_override_a_preexisting_item(): void
    {
        $this->stash->put('override', 'objection');
        $this->stash->put('override', 'overruled');

        $this->assertEquals('overruled', $this->stash->get('override'));
    }

    #[Test]
    public function it_can_add_and_retrieve_an_item_forever(): void
    {
        $this->assertTrue($this->stash->forever('diamonds', ['Hope', 'Pink Panther', 'Tiffany']));
        $this->assertEquals(['Hope', 'Pink Panther', 'Tiffany'], $this->stash->get('diamonds'));
    }

    #[Test]
    public function it_returns_false_for_an_expired_item(): void
    {
        $this->stash->put('expired', 'qwerty', -1);

        $this->assertFalse($this->stash->get('expired'));
    }

    #[Test]
    public function it_returns_false_for_nonexistant_items(): void
    {
        $this->assertFalse($this->stash->get('nonexistant-item'));
    }

    #[Test]
    public function it_returns_a_default_value(): void
    {
        $this->assertNull($this->stash->get('nonexistant-item', null));
    }

    #[Test]
    public function it_returns_true_if_it_has_an_item(): void
    {
        $this->stash->put('has', 'some-item', TTL::minutes(5));

        $this->assertTrue($this->stash->has('has'));
    }

    #[Test]
    public function it_returns_true_if_it_has_a_boolean_false(): void
    {
        $this->stash->put('false', false, TTL::minutes(5));

        $this->assertTrue($this->stash->has('false'));
    }

    #[Test]
    public function it_returns_false_if_it_doesnt_have_an_item(): void
    {
        $this->assertFalse($this->stash->has('nonexistant-item'));
    }

    #[Test]
    public function it_remembers_an_already_cached_item(): void
    {
        $this->stash->put('remember-pre-existing', "Don't override me bro!", TTL::minutes(5));

        $text = $this->stash->remember('remember-pre-existing', TTL::minutes(5), fn (): string => "I wont't override him.");

        $this->assertEquals("Don't override me bro!", $text);
    }

    #[Test]
    public function it_remembers_an_already_cached_boolean_false(): void
    {
        $this->stash->put('boolean', false);

        $boolean = $this->stash->remember('boolean', TTL::minutes(5), fn (): string => 'Not boolean false');

        $this->assertFalse($boolean);
    }

    #[Test]
    public function it_remembers_a_nonexistant_item(): void
    {
        $date = $this->stash->remember('remember-new-item', TTL::minutes(5), fn (): string => 'Pork Chops');

        $this->assertEquals('Pork Chops', $date);
    }

    #[Test]
    public function it_remembers_an_already_cached_item_forever(): void
    {
        $this->stash->put('remember-forever-pre-existing', 'I already exist', TTL::minutes(5));

        $text = $this->stash->rememberForever('remember-forever-pre-existing', fn (): string => "I don't yet exist.");

        $this->assertEquals('I already exist', $text);
    }

    #[Test]
    public function it_remembers_a_nonexistant_item_forever(): void
    {
        $date = $this->stash->rememberForever('remember-remember', fn (): string => 'November 5th');

        $this->assertEquals('November 5th', $date);
    }

    #[Test]
    public function it_can_increment_an_item(): void
    {
        $this->stash->put('inc', 1336);

        $this->assertEquals(1337, $this->stash->increment('inc'));
    }

    #[Test]
    public function it_can_increment_an_item_by_a_custom_ammount(): void
    {
        $this->stash->put('inc-custom', 1327);

        $this->assertEquals(1337, $this->stash->increment('inc-custom', 10));
    }

    #[Test]
    public function it_can_decrement_an_item(): void
    {
        $this->stash->put('dec', 1338);

        $this->assertEquals(1337, $this->stash->decrement('dec'));
    }

    #[Test]
    public function it_can_decrement_an_item_by_a_custom_ammount(): void
    {
        $this->stash->put('dec-custom', 1347);

        $this->assertEquals(1337, $this->stash->decrement('dec-custom', 10));
    }

    #[Test]
    public function it_returns_false_when_incrementing_a_non_integer(): void
    {
        $this->stash->put('non-integer', 'potato');

        $this->assertFalse($this->stash->increment('non-integer'));
    }

    #[Test]
    public function it_returns_false_when_decrementing_a_non_integer(): void
    {
        $this->stash->put('non-integer', 'potato');

        $this->assertFalse($this->stash->decrement('non-integer'));
    }

    #[Test]
    public function it_returns_false_when_incrementing_a_nonexistant_item(): void
    {
        $this->assertFalse($this->stash->increment('nonexistant-item'));
    }

    #[Test]
    public function it_returns_false_when_decrementing_a_nonexistant_item(): void
    {
        $this->assertFalse($this->stash->decrement('nonexistant-item'));
    }

    #[Test]
    public function it_can_set_a_new_expiration_time_for_an_item(): void
    {
        $this->stash->put('extendable', 'tape measure', TTL::minutes(1));

        $this->assertTrue($this->stash->touch('extendable', TTL::minutes(5)));
    }

    #[Test]
    public function it_sets_an_item_to_false_when_touchng_a_nonexistant_item(): void
    {
        $this->stash->touch('nonexistent');

        $this->assertTrue($this->stash->has('nonexistent'));
        $this->assertFalse($this->stash->get('nonexistent'));
    }

    #[Test]
    public function it_can_set_a_new_expiration_time_for_an_array_of_items(): void
    {
        $this->stash->put('extendable', 'tape measure', TTL::minutes(1));
        $this->stash->put('growable', 'plant', TTL::minutes(1));

        $this->assertTrue($this->stash->touch(['extendable', 'growable'], TTL::minutes(5)));
    }

    #[Test]
    public function it_can_forget_an_item(): void
    {
        $this->stash->put('forgettable', 'asdf', TTL::minutes(5));

        $this->assertTrue($this->stash->forget('forgettable'));
        $this->assertFalse($this->stash->has('forgettable'));
        $this->assertFalse($this->stash->get('forgettable'));
    }

    #[Test]
    public function it_returns_false_when_forgetting_a_nonexistent_item(): void
    {
        $this->assertFalse($this->stash->forget('nonexistant-item'));
    }

    #[Test]
    public function it_can_forget_an_array_of_items(): void
    {
        $this->stash->put('foo', 'foo', TTL::minutes(5));
        $this->stash->put('bar', 'bar', TTL::minutes(5));
        $this->stash->put('baz', 'baz', TTL::minutes(5));

        $this->assertTrue($this->stash->forget(['foo', 'bar']));
        $this->assertFalse($this->stash->has('foo'));
        $this->assertFalse($this->stash->has('bar'));
        $this->assertTrue($this->stash->has('baz'));
    }

    #[Test]
    public function it_is_flushable(): void
    {
        $this->assertTrue($this->stash->flush());
    }
}
