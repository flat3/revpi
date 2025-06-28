<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Monitors\TrendMonitor;
use PHPUnit\Framework\TestCase;

class TrendMonitorTest extends TestCase
{
    public function test_returns_false_until_window_is_full(): void
    {
        $monitor = new TrendMonitor(3);

        self::assertFalse($monitor->evaluate(1));
        self::assertFalse($monitor->evaluate(2));
        // Window is now [1,2], size is 3, still not full
    }

    public function test_returns_true_on_strictly_increasing_sequence(): void
    {
        $monitor = new TrendMonitor(3);

        self::assertFalse($monitor->evaluate(1));
        self::assertFalse($monitor->evaluate(2));

        // Now window is [1,2]
        self::assertTrue($monitor->evaluate(3));
        // Now window is [1,2,3], strictly increasing
    }

    public function test_returns_false_on_equal_or_decreasing_sequence(): void
    {
        $monitor = new TrendMonitor(3);

        self::assertFalse($monitor->evaluate(3));
        self::assertFalse($monitor->evaluate(2));
        self::assertFalse($monitor->evaluate(1));
        // [3,2,1] is not increasing

        // Try a plateau
        $monitor = new TrendMonitor(3);

        self::assertFalse($monitor->evaluate(1));
        self::assertFalse($monitor->evaluate(2));
        self::assertFalse($monitor->evaluate(2)); // 2 == 2, not strictly increasing

        // Try a peak then down
        $monitor = new TrendMonitor(3);

        self::assertFalse($monitor->evaluate(1));
        self::assertFalse($monitor->evaluate(3));
        self::assertFalse($monitor->evaluate(2)); // 3 -> 2 is not increasing
    }

    public function test_shifts_old_values(): void
    {
        $monitor = new TrendMonitor(3);
        $monitor->evaluate(1); // window: [1]
        $monitor->evaluate(2); // window: [1,2]

        self::assertTrue($monitor->evaluate(3)); // window: [1,2,3], strictly increasing

        // Next, input 4, window: [2,3,4]
        self::assertTrue($monitor->evaluate(4)); // window: [2,3,4]

        // Next, input 3, window: [3,4,3] (should fail)
        self::assertFalse($monitor->evaluate(3));
    }
}
