<?php

namespace PHLAK\Stash\Tests;

use PHPUnit\Framework\TestCase;
use PHLAK\Stash;
use PHLAK\Stash\Tests\Traits\Cacheable;

class APCuTest extends TestCase
{
    use Cacheable;

    protected $stash;

    public function setUp()
    {
        $this->stash = new Stash\Drivers\APCu();
    }
}
