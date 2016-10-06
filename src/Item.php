<?php

namespace Stash;

use Carbon\Carbon;

class Item
{
    /** @var mixed Item data */
    protected $data;

    /** @var Carbon Carbon instance representing the expiration time */
    protected $expires;

    /**
     * Stash\Item constructor, runs on object creation
     *
     * @param mixed $data    Item data
     * @param int   $minutes Time in minutes until item expires
     */
    public function __construct($data, $minutes = 0)
    {
        $this->data    = $data;
        $this->expires = $minutes == 0 ? Carbon::maxValue() : Carbon::now()->addMinutes($minutes);
    }

    /**
     * Magic getter method, allows retrieving of class property values
     *
     * @param  string $property Property name
     *
     * @return mixed            Property value
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Determine if this item has expired
     *
     * @return bool True if expired, otherwise false
     */
    public function expired()
    {
        return Carbon::now()->gt($this->expires);
    }

    /**
     * Determine if this item has not expired
     *
     * @return bool True if not expired, otherwise false
     */
    public function notExpired()
    {
        return Carbon::now()->lte($this->expires);
    }

    /**
     * [increment description]
     *
     * @param  integer $value [description]
     *
     * @return [type]         [description]
     */
    public function increment($value = 1)
    {
        if ($this->notExpired() && is_int($this->data)) {
            $this->data += $value;
            return $this->data;
        }

        return false;
    }

    /**
     * [decrement description]
     *
     * @param  integer $value [description]
     *
     * @return [type]         [description]
     */
    public function decrement($value = 1)
    {
        return $this->increment($value * -1);
    }
}
