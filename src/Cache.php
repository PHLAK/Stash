<?php

namespace PHLAK\Stash;

use PHLAK\Stash\Exceptions\InvalidDriverException;

class Cache
{
    /**
     * Initialize the desired cache driver object.
     *
     * @param string $driver Driver to initialize
     * @param array  $config Array of configuration options
     *
     * @throws InvalidDriverException;
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
                $options = array_merge([
                    \Memcached::OPT_PREFIX_KEY => $prefix
                ], @$config['options'] ?: []);

                return new Drivers\Memcached($config['servers'], $options);

            case 'redis':
                return new Drivers\Redis($config['servers'], $prefix);

            case 'ephemeral':
                return new Drivers\Ephemeral($prefix);

            default:
                throw new InvalidDriverException;
        }
    }
}
