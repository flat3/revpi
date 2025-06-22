<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Flat3\RevPi\Interfaces\Monitor;

class DigitalMonitor implements Monitor
{
    public function evaluate(mixed $previous, mixed $next): bool
    {
        return $previous !== $next;
    }
}
