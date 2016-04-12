<?php

class CacheTest extends PHPUnit_Framework_TestCase {

    protected $apc;
    protected $file;
    protected $memcached;

    public function setUp() {
        $this->apc      = Stash\Cache::make('apc', ['prefix' => 'stash_test']);
        $this->file     = Stash\Cache::make('file', ['dir' => __DIR__ . '/cache']);
        $this->memcached = Stash\Cache::make('memcached', [['host' => 'localhost', 'port' => 11211]]);
    }

    /** @test */
    public function it_is_an_instance_of_the_correct_class() {
        $this->assertInstanceOf('Stash\Drivers\Apc', $this->apc);
        $this->assertInstanceOf('Stash\Drivers\File', $this->file);
        $this->assertInstanceOf('Stash\Drivers\Memcached', $this->memcached);
    }

    /** @test */
    public function can_make_apc_without_a_config() {
        $this->assertInstanceOf('Stash\Drivers\Apc', Stash\Cache::make('apc'));
    }

}
