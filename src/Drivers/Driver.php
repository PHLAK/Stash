<?php

namespace Stash\Drivers;

use Stash\Interfaces\Cacheable;

abstract class Driver implements Cacheable {

    /** @var string Key prefix for preventing collisions */
    protected $prefix;

    /**
     * Get prefixed key
     *
     * @param  string $key Unique item identifier
     *
     * @return string      Prefixed unique identifier
     */
    protected function prefix($key) {
        return empty($this->prefix) ? $key : $this->prefix . '_' . $key;
    }

}