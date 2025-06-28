<?php

namespace Flat3\RevPi\Monitors;

class RangeMonitor extends Monitor
{
    public function __construct(protected int|float $min, protected int|float $max) {}

    public function evaluate(int|bool $next): bool
    {
        if ($next < $this->min || $next > $this->max) {
            return true;
        }

        return false;
    }
}
