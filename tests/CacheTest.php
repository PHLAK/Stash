<?php

class CacheTest extends PHPUnit_Framework_TestCase {

    /** @test */
    public function it_can_instantiate_the_apc_driver() {
        $apc = Stash\Cache::make('apcu');
        $this->assertInstanceOf('Stash\Drivers\Driver', $apc);
        $this->assertInstanceOf('Stash\Drivers\APCu', $apc);
    }

    /** @test */
    public function it_can_instantiate_the_apc_driver_with_prefix() {
        $apc = Stash\Cache::make('apcu', ['prefix' => 'stash_test']);
        $this->assertInstanceOf('Stash\Drivers\Driver', $apc);
        $this->assertInstanceOf('Stash\Drivers\APCu', $apc);
    }

    /** @test */
    public function it_can_instantiate_the_file_driver() {
        $file = Stash\Cache::make('file', ['dir' => __DIR__ . '/cache']);
        $this->assertInstanceOf('Stash\Drivers\Driver', $file);
        $this->assertInstanceOf('Stash\Drivers\File', $file);
    }

    /** @test */
    public function it_can_instantiate_the_file_driver_with_prefix() {
        $file = Stash\Cache::make('file', [
            'dir'    => __DIR__ . '/cache',
            'prefix' => 'stash_test'
        ]);
        $this->assertInstanceOf('Stash\Drivers\Driver', $file);
        $this->assertInstanceOf('Stash\Drivers\File', $file);
    }

    /** @test */
    public function it_can_instantiate_the_memcached_driver() {
        $memcached = Stash\Cache::make('memcached', [
            'servers' => [
                ['host' => 'localhost', 'port' => 11211]
            ]
        ]);
        $this->assertInstanceOf('Stash\Drivers\Driver', $memcached);
        $this->assertInstanceOf('Stash\Drivers\Memcached', $memcached);
    }

    /** @test */
    public function it_can_instantiate_the_memcached_driver_with_prefix() {
        $memcached = Stash\Cache::make('memcached', [
                'servers' => [
                    ['host' => 'localhost', 'port' => 11211]
                ],
                'prefix' => 'stash_test'
        ]);
        $this->assertInstanceOf('Stash\Drivers\Driver', $memcached);
        $this->assertInstanceOf('Stash\Drivers\Memcached', $memcached);
    }

}
