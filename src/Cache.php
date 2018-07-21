<?php

namespace PHLAK\Stash;

use Closure;
use PHLAK\Stash\Exceptions\InvalidDriverException;

class Cache
{
    /**
     * Instantiate the desired cache driver object.
     *
     * @param string  $driver Driver to initialize
     * @param Closure $config Driver-specific configuration closure
     *
     * @throws \PHLAK\Stash\Exceptions\InvalidDriverException;
     *
     * @return \PHLAK\Stash\Interfaces\Cacheable A Cacheable object
     */
    public static function make($driver, Closure $config = null)
    {
        trigger_error('The Stash::make() method has been deprecated and will be'
            . ' removed in a future version. Use a specific named-constructor'
            . ' instead.', E_USER_DEPRECATED);

        if (! method_exists(__CLASS__, $driver)) {
            throw new InvalidDriverException('Unable to initialize driver of type ' . $driver);
        }

        return self::$driver($config);
    }

    /**
     * Instantiate the APCu cache driver object.
     *
     * @param Closure|null $config A configuration closure
     *
     * @return \PHLAK\Stash\Drivers\APCu An APCu cache object
     */
    public static function apcu(Closure $config = null)
    {
        return new Drivers\APCu($config);
    }

    /**
     * Instantiate the File cache driver object.
     *
     * @param Closure $config A configuration closure
     *
     * @return \PHLAK\Stash\Drivers\File A File cache object
     */
    public static function file(Closure $config)
    {
        return new Drivers\File($config);
    }

    /**
     * Instantiate the Memcached cache driver object.
     *
     * @param Closure $config A configuration closure
     *
     * @return \PHLAK\Stash\Drivers\Memcached A Memcached cache object
     */
    public static function memcached(Closure $config)
    {
        return new Drivers\Memcached($config);
    }

    /**
     * Instantiate the Redis cache driver object.
     *
     * @param Closure $config A configuration closure
     *
     * @return \PHLAK\Stash\Drivers\Redis A Redis cache object
     */
    public static function redis(Closure $config)
    {
        return new Drivers\Redis($config);
    }

    /**
     * Instantiate the Ephemeral cache driver object.
     *
     * @param Closure $config A configuration closure
     *
     * @return \PHLAK\Stash\Drivers\Ephemeral An Ephemeral cache object
     */
    public static function ephemeral()
    {
        return new Drivers\Ephemeral();
    }
}
