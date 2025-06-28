<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Monitors\MovingAverageMonitor;
use PHPUnit\Framework\TestCase;

class MovingAverageMonitorTest extends TestCase
{
    public function test_evaluate_returns_false_when_window_is_empty(): void
    {
        $monitor = new MovingAverageMonitor(windowSize: 3, threshold: 1.5);

        // No values yet, but window is managed after input
        self::assertFalse($monitor->evaluate(0), 'First value should NOT trigger threshold');
    }

    public function test_evaluate_returns_false_when_below_threshold(): void
    {
        $monitor = new MovingAverageMonitor(windowSize: 3, threshold: 10.0);

        $monitor->evaluate(1);
        $monitor->evaluate(2);
        $monitor->evaluate(3);

        self::assertFalse($monitor->evaluate(4), 'Average is below threshold');
    }

    public function test_evaluate_returns_true_when_above_threshold(): void
    {
        $monitor = new MovingAverageMonitor(windowSize: 3, threshold: 2.0);

        $monitor->evaluate(3);
        $monitor->evaluate(4);

        // Average of [3,4] is 3.5 > 2.0
        self::assertTrue($monitor->evaluate(4), 'Average should be above threshold');
    }

    public function test_evaluate_shifts_window_correctly(): void
    {
        $monitor = new MovingAverageMonitor(windowSize: 2, threshold: 3.0);

        $monitor->evaluate(2); // window: [2]

        self::assertFalse($monitor->evaluate(3)); // window: [2,3] average 2.5
        self::assertTrue($monitor->evaluate(5)); // window: [3,5] average 4.0
    }

    public function test_evaluate_handles_boolean(): void
    {
        $monitor = new MovingAverageMonitor(windowSize: 2, threshold: 0.5);

        self::assertFalse($monitor->evaluate(false)); // 0
        self::assertFalse($monitor->evaluate(true));  // [0,1] average 0.5 (not greater than 0.5)
        self::assertTrue($monitor->evaluate(true));  // [1,1] average 1 > 0.5
    }
}
