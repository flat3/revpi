<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Flat3\RevPi\Interfaces\Monitor;

class TrendMonitor implements Monitor
{
    /**
     * @var array<int|bool>
     */
    protected array $window = [];

    public function __construct(protected int $size) {}

    public function evaluate(int|bool $next): bool
    {
        $this->window[] = $next;

        if (count($this->window) > $this->size) {
            array_shift($this->window);
        }

        if (count($this->window) < $this->size) {
            return false;
        }

        for ($i = 1; $i < $this->size; $i++) {
            if ($this->window[$i] <= $this->window[$i - 1]) {
                return false;
            }
        }

        return true;
    }
}
