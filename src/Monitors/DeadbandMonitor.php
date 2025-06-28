<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Flat3\RevPi\Exceptions\NotSupportedException;

class DeadbandMonitor extends Monitor
{
    protected bool $wasOutside = false;

    public function __construct(protected float $center, protected float $deadband) {}

    public function evaluate(int|bool $next): bool
    {
        if (is_bool($next)) {
            throw new NotSupportedException;
        }

        $outside = abs($next - $this->center) > $this->deadband;
        $trigger = ($outside !== $this->wasOutside) && $outside;
        $this->wasOutside = $outside;

        return $trigger;
    }
}
