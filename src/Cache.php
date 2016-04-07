<?php

namespace Stash;

class Cache {

    public static function make($driver, array $config = []) {

        switch ($driver) {

            case 'apc':
                return new Apc($config['prefix']);

            case 'file':
                return new File($config['dir']);

            case 'memcache':
                return new Memcache($config['host'], $config['port']);

            default:
                throw new \Exception('Invalid driver supplied');

        }

    }

}
