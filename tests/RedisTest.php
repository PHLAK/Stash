<?php

use PHLAK\Stash;

class RedisTest extends PHPUnit_Framework_TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\Redis(function ($redis) {
            $redis->pconnect('localhost', 6379);

            return $redis;
        });
    }

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);

        $this->assertFalse($this->stash->get('expired'));
    }
}
