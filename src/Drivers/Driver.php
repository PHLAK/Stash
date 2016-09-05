<?php

namespace Stash\Drivers;

use Stash\Interfaces\Cacheable;

abstract class Driver implements Cacheable
{
    /** @var string Key prefix for preventing collisions */
    protected $prefix;

    /**
     * Stash\Driver constructor, runs on object creation
     *
     * @param string $prefix Key prefix for preventing collisions
     */
    public function __construct($prefix = '') {
        $this->prefix = $prefix;
    }

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
