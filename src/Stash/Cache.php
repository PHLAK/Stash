<?php

namespace Stash;

use Stash\Exceptions\InvalidDriverException;

class Cache
{
    /**
     * Initialize the desired cache driver object.
     *
     * @param string $driver Driver to initialize
     * @param array  $config Array of configuration options
     *
     * @return object Cacheable Object
     */
    public static function make($driver, array $config = [])
    {
        $prefix = @$config['prefix'] ?: '';

        switch ($driver) {
            case 'apcu':
                return new Drivers\APCu($prefix);

            case 'file':
                return new Drivers\File($config['dir'], $prefix);

            case 'memcached':
                return new Drivers\Memcached($config['servers'], $prefix);

            case 'ephemeral':
                return new Drivers\Ephemeral($prefix);

            default:
                throw new InvalidDriverException;
        }
    }
}
