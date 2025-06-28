<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Flat3\RevPi\Exceptions\NotSupportedException;

class RateOfChangeMonitor extends Monitor
{
    protected ?int $previous = null;

    public function __construct(protected float $maxRate)
    {
        if ($maxRate <= 0) {
            throw new NotSupportedException('maxRate must be positive');
        }
    }

    public function evaluate(int|bool $next): bool
    {
        if (is_bool($next)) {
            throw new NotSupportedException;
        }

        $result = false;

        if ($this->previous !== null) {
            $rate = abs($next - $this->previous);
            $result = $rate >= $this->maxRate;
        }

        $this->previous = $next;

        return $result;
    }
}
