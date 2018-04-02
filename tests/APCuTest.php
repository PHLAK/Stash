<?php

use PHPUnit\Framework\TestCase;
use PHLAK\Stash;

class APCuTest extends TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\APCu();
    }
}
