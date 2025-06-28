<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

class DigitalMonitor extends Monitor
{
    protected int|bool|null $previous = null;

    public function evaluate(int|bool $next): bool
    {
        $previous = $this->previous;
        $this->previous = $next;

        if ($previous === null || $previous === $next) {
            return false;
        }

        return true;
    }
}
