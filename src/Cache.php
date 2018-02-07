<?php

namespace PHLAK\Stash;

use Closure;
use PHLAK\Stash\Exceptions\InvalidDriverException;

class Cache
{
    /** @var array Array of drivers and their respective class paths */
    protected static $drivers = [
        'apcu' => Drivers\APCu::class,
        'ephemeral' => Drivers\Ephemeral::class,
        'file' => Drivers\File::class,
        'memcached' => Drivers\Memcached::class,
        'redis' => Drivers\Redis::class
    ];

    /**
     * Instantiate the desired cache driver object.
     *
     * @param string  $driver Driver to initialize
     * @param Closure $config Driver-specific configuration closure
     *
     * @throws InvalidDriverException;
     *
     * @return Interfaces\Cacheable A Cacheable object
     */
    public static function make($driver, Closure $config = null)
    {
        if (! array_key_exists($driver, self::$drivers)) {
            throw new InvalidDriverException;
        }

        return new self::$drivers[$driver]($config);
    }

    /**
     * Instantiate the APCu cache driver object.
     *
     * @param Closure|null $config A configuration closure
     *
     * @return Drivers\APCu An APCu cache object
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
     * @return Drivers\File A File cache object
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
     * @return Drivers\Memcached An Memcached cache object
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
     * @return Drivers\Redis An Redis cache object
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
     * @return Drivers\Ephemeral An Ephemeral cache object
     */
    public static function ephemeral()
    {
        return new Drivers\Ephemeral();
    }
}
