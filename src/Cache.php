<?php

namespace PHLAK\Stash;

use Closure;
use PHLAK\Stash\Drivers\APCu;
use PHLAK\Stash\Drivers\Ephemeral;
use PHLAK\Stash\Drivers\File;
use PHLAK\Stash\Drivers\Memcached;
use PHLAK\Stash\Drivers\Redis;

class Cache
{
    /**
     * Instantiate the APCu cache driver object.
     *
     * @param Closure|null $config A configuration closure
     *
     * @return APCu An APCu cache object
     */
    public static function apcu(?Closure $config = null): APCu
    {
        return new Drivers\APCu($config);
    }

    /**
     * Instantiate the File cache driver object.
     *
     * @param Closure $config A configuration closure
     *
     * @return File A File cache object
     */
    public static function file(Closure $config): File
    {
        return new Drivers\File($config);
    }

    /**
     * Instantiate the Memcached cache driver object.
     *
     * @param Closure $config A configuration closure
     *
     * @return Memcached A Memcached cache object
     */
    public static function memcached(Closure $config): Memcached
    {
        return new Drivers\Memcached($config);
    }

    /**
     * Instantiate the Redis cache driver object.
     *
     * @param Closure $config A configuration closure
     *
     * @return Redis A Redis cache object
     */
    public static function redis(Closure $config): Redis
    {
        return new Drivers\Redis($config);
    }

    /**
     * Instantiate the Ephemeral cache driver object.
     *
     * @return Ephemeral An Ephemeral cache object
     */
    public static function ephemeral(): Ephemeral
    {
        return new Drivers\Ephemeral;
    }
}
