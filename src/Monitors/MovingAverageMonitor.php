<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

class MovingAverageMonitor extends Monitor
{
    /**
     * @var array<int|bool>
     */
    protected array $window = [];

    public function __construct(protected int $windowSize, protected float $threshold) {}

    public function evaluate(int|bool $next): bool
    {
        $this->window[] = $next;

        if (count($this->window) > $this->windowSize) {
            array_shift($this->window);
        }

        if (count($this->window) === 0) {
            return false;
        }

        $average = array_sum($this->window) / count($this->window);

        return $average > $this->threshold;
    }
}
