<?php

namespace App\Services;

class Pipeline
{
    public static function make(array $parts): string
    {
        return implode(' | ', $parts);
    }
}
