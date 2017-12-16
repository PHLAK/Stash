<?php

namespace PHLAK\Stash;

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
     * Initialize the desired cache driver object.
     *
     * @param string   $driver Driver to initialize
     * @param \Closure $config Driver-specific anonymous configuration function
     *
     * @throws InvalidDriverException;
     *
     * @return object Cacheable Object
     */
    public static function make($driver, \Closure $config = null)
    {
        if (! array_key_exists($driver, self::$drivers)) {
            throw new InvalidDriverException;
        }

        return new self::$drivers[$driver]($config);
    }
}
