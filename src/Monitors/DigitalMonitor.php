<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Flat3\RevPi\Interfaces\Monitor;

class DigitalMonitor implements Monitor
{
    protected int|bool|null $previous = null;

    public function evaluate(int|bool|null $next): bool
    {
        $previous = $this->previous;
        $this->previous = $next;

        if ($previous === null || $previous === $next) {
            return false;
        }

        return true;
    }
}
