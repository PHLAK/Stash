<?php

class FileTest extends PHPUnit_Framework_TestCase {

    protected $stash;

    public function setUp() {
        $this->stash = new Stash\File(__DIR__ . '/cache');
    }

    /** @test */
    public function it_can_add_and_retrieve_an_item() {
        $this->assertTrue($this->stash->put('foo', 'Foo', 5));
        $this->assertEquals('Foo', $this->stash->get('foo'));
    }

    /** @test */
    public function it_can_add_and_retrieve_an_item_forever() {
        $this->assertTrue($this->stash->forever('diamonds', ['Hope', 'Pink Panther', 'Tiffany']));
        $this->assertEquals(['Hope', 'Pink Panther', 'Tiffany'], $this->stash->get('diamonds'));
    }

    /** @test */
    public function it_returns_null_for_nonexistant_items() {
        $this->assertFalse($this->stash->get('nonexistant-item'));
    }

    /** @test */
    public function it_returns_a_default_value() {
        $this->assertEquals('Default', $this->stash->get('nonexistant-item', 'Default'));
    }

    /** @test */
    public function it_returns_true_if_it_has_an_item() {
        $this->assertTrue($this->stash->put('item', 'some-item', 5));
        $this->assertTrue($this->stash->has('item'));
    }

    /** @test */
    public function it_returns_false_if_it_doesnt_have_an_item() {
        $this->assertFalse($this->stash->has('nonexistant-item'));
    }

    /** @test */
    public function it_remembers_an_already_cached_item() {

        $this->stash->put('remember-me', "Don't override me bro!", 5);

        $text = $this->stash->remember('remember-me', 5, function() {
            return "I don't override him.";
        });

        $this->assertEquals("Don't override me bro!", $text);

    }

    /** @test */
    public function it_remembers_a_nonexistant_item() {
        $this->assertEquals('November 5th', $this->stash->remember('remember-remember', 5, function() {
            return 'November 5th';
        }));
    }

    /** @test */
    public function it_can_forget_an_item() {
        $this->stash->put('forgettable', 'asdf', 5);
        $this->assertTrue($this->stash->forget('forgettable'));
        $this->assertFalse($this->stash->has('forgettable'));
        $this->assertFalse($this->stash->get('forgettable'));
    }

}
