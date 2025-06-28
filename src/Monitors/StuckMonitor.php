<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Flat3\RevPi\Interfaces\Monitor;

class StuckMonitor implements Monitor
{
    protected int|bool|null $previous = null;

    protected int $sameCount = 0;

    public function __construct(protected int $repeatCount) {}

    public function evaluate(int|bool $next): bool
    {
        if ($next === $this->previous) {
            $this->sameCount++;
        } else {
            $this->sameCount = 1;
        }

        $this->previous = $next;

        return $this->sameCount >= $this->repeatCount;
    }
}
