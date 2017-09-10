<?php

use PHLAK\Stash;

class APCuTest extends PHPUnit_Framework_TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\APCu();
    }
}
