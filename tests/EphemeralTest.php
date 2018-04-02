<?php

use PHPUnit\Framework\TestCase;
use PHLAK\Stash;

class EphemeralTest extends TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\Ephemeral();
    }

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);
        $this->assertFalse($this->stash->get('expired'));
    }
}
