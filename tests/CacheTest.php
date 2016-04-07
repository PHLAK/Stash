<?php

class CacheTest extends PHPUnit_Framework_TestCase {

    protected $apc;
    protected $file;
    protected $memcache;

    public function setUp() {
        $this->apc      = Stash\Cache::make('apc', ['prefix' => 'stash_test']);
        $this->file     = Stash\Cache::make('file', ['dir' => __DIR__ . '/cache']);
        $this->memcache = Stash\Cache::make('memcache', ['host' => 'localhost', 'port' => '11211']);
    }

    /** @test */
    public function it_is_an_instance_of_the_correct_class() {
        $this->assertInstanceOf('Stash\Apc', $this->apc);
        $this->assertInstanceOf('Stash\File', $this->file);
        $this->assertInstanceOf('Stash\Memcache', $this->memcache);
    }

    /** @test */
    public function can_make_apc_without_a_config() {
        $this->assertInstanceOf('Stash\Apc', Stash\Cache::make('apc'));
    }

}
