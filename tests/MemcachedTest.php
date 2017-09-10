<?php

use PHLAK\Stash;

class MemcachedTest extends PHPUnit_Framework_TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\Memcached([
            ['host' => 'localhost', 'port' => 11211]
        ]);
    }

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);
        $this->assertFalse($this->stash->get('expired'));
    }
}
