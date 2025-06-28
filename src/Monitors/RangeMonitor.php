<?php

namespace Flat3\RevPi\Monitors;

use Flat3\RevPi\Interfaces\Monitor;

class RangeMonitor implements Monitor
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
