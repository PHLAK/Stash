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
     * [__construct description]
     *
     * @param [type]  $data    [description]
     * @param integer $minutes [description]
     */
    public function __construct($data, $minutes = 0)
    {
        $this->data    = $data;
        $this->expires = $minutes > 0 ? Carbon::now()->addMinutes($minutes) : Carbon::maxValue();
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
