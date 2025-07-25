<?php

namespace PHLAK\Stash;

use Carbon\CarbonImmutable;

class Item
{
    /** @var CarbonImmutable instance representing the expiration time */
    public readonly CarbonImmutable $expires;

    /**
     * @param $data Item data
     * @param $seconds Time in seconds until item expires
     */
    public function __construct(
        public mixed $data,
        public int $seconds = 0
    ) {
        $this->expires = $seconds === 0 ? new CarbonImmutable('9999-12-31 23:59:59') : CarbonImmutable::now()->addSeconds($seconds);
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
