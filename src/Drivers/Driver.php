<?php

namespace PHLAK\Stash\Drivers;

use PHLAK\Stash\Interfaces\Cacheable;

abstract class Driver implements Cacheable
{
    /** @var array Array of configuration options */
    protected $config;

    /**
     * Stash\Drivers\Driver constructor, runs on object creation.
     *
     * @param \Closure|null $closure Anonymous configuration function
     */
    public function __construct(\Closure $closure = null)
    {
        $this->config = is_callable($closure) ? $closure() : ['prefix' => ''];
    }

    /**
     * Get prefixed key.
     *
     * @param string $key Unique item identifier
     *
     * @return string Prefixed unique identifier
     */
    protected function prefix($key)
    {
        return empty($this->config['prefix']) ? $key : $this->config['prefix'] . '_' . $key;
    }
}
