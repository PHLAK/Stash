<?php

class FileTest extends PHPUnit_Framework_TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\File(__DIR__ . '/cache');
    }

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);
        $this->assertFalse($this->stash->get('expired'));
    }
}
