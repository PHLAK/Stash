<?php

namespace PHLAK\Stash\Helpers;

class TTL
{
    public static function seconds(int $seconds): int
    {
        return $seconds;
    }

    public static function minutes(int $minutes): int
    {
        return $minutes * 60;
    }

    public static function hours(int $hours): int
    {
        return $hours * 60 * 60;
    }

    public static function days(int $days): int
    {
        return $days * 60 * 60 * 24;
    }
}
