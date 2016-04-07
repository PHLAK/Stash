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
                return isset($config['prefix']) ? new Apc($config['prefix']) : new Apc();

            case 'file':
                return new File($config['dir']);

            case 'memcache':
                return new Memcache($config['host'], $config['port']);

            default:
                throw new \Exception('Invalid driver supplied');

        }

    }

}
