<?php

namespace App\Services;

abstract class AbstractExecutor
{
    public static function make(): static
    {
        return new static();
    }
}
