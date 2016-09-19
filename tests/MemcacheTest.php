<?php

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
}
