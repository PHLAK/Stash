<?php

namespace Stash;

class Cache {

    /**
     * Initialize the desired cache driver object
     *
     * @param  string $driver Driver to initialize
     * @param  array  $config Array of configuration options
     *
     * @return object         Cache Object
     */
    public static function make($driver, array $config = []) {

        $prefix = @$config['prefix'] ?: '';

        switch ($driver) {

            case 'apc':
                return new Drivers\Apc($prefix);

            case 'file':
                return new Drivers\File($config['dir'], $prefix);

            case 'memcached':
                return new Drivers\Memcached($config['servers'], $prefix);

            default:
                throw new \Exception('Invalid driver supplied');

        }

    }

}
