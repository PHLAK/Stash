<?php

namespace PHLAK\Stash;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class Item
{
    /** @var Carbon instance representing the expiration time */
    private CarbonInterface $expires;

    /**
     * Stash\Item constructor, runs on object creation.
     *
     * @param $data Item data
     * @param $minutes Time in minutes until item expires
     */
    public function __construct(
        private mixed $data,
        int $minutes = 0
    ) {
        $this->expires = $minutes === 0 ? new CarbonImmutable('9999-12-31 23:59:59') : CarbonImmutable::now()->addMinutes($minutes);
    }

    /**
     * Magic getter method, allows retrieving of class property values.
     *
     * @param string $property Property name
     *
     * @return mixed Property value
     */
    public function __get($property): mixed
    {
        return $this->$property;
    }

    /**
     * Determine if this item has expired.
     *
     * @return bool True if expired, otherwise false
     */
    public function expired(): bool
    {
        return CarbonImmutable::now()->isAfter($this->expires);
    }

    /**
     * Determine if this item has not expired.
     *
     * @return bool True if not expired, otherwise false
     */
    public function notExpired(): bool
    {
        return ! $this->expired();
    }

    /**
     * Increase the value of a stored integer.
     *
     * @param int $value The ammount by which to decrement
     *
     * @return mixed The new value on success, otherwise false
     */
    public function increment($value = 1): mixed
    {
        if ($this->notExpired() && is_int($this->data)) {
            $this->data += $value;

            return $this->data;
        }

        return false;
    }

    /**
     * Decrease the value of a stored integer.
     *
     * @param int $value The amount by which to decrement
     *
     * @return mixed The new value on success, otherwise false
     */
    public function decrement($value = 1): mixed
    {
        return $this->increment($value * -1);
    }
}
