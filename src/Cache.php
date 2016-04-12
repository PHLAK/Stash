<?php

namespace Stash;

class Cache {

    /**
     * Initialize the desired cache driver
     *
     * @param  string $driver Driver to initialize
     * @param  array  $config Array of configuration options
     *
     * @return object         Cache Object
     */
    public static function make($driver, array $config = []) {

        switch ($driver) {

            case 'apc':
                return isset($config['prefix']) ? new Drivers\Apc($config['prefix']) : new Drivers\Apc();

            case 'file':
                return new Drivers\File($config['dir']);

            case 'memcache':
            case 'memcached':
                return new Drivers\Memcached($config);

            default:
                throw new \Exception('Invalid driver supplied');

        }

    }

}
