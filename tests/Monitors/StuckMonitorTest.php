<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Monitors\StuckMonitor;
use PHPUnit\Framework\TestCase;

class StuckMonitorTest extends TestCase
{
    public function test_returns_false_if_not_repeated(): void
    {
        $monitor = new StuckMonitor(3);

        self::assertFalse($monitor->evaluate(5));
        self::assertFalse($monitor->evaluate(6));
        self::assertFalse($monitor->evaluate(7));
    }

    public function test_returns_true_on_repeat_count(): void
    {
        $monitor = new StuckMonitor(3);

        self::assertFalse($monitor->evaluate(2));
        self::assertFalse($monitor->evaluate(2));
        self::assertTrue($monitor->evaluate(2));
    }

    public function test_resets_count_on_new_value(): void
    {
        $monitor = new StuckMonitor(2);

        self::assertFalse($monitor->evaluate(10)); // 1st time, count=1, false
        self::assertTrue($monitor->evaluate(10));  // 2nd time, count=2, true

        // Switch value
        self::assertFalse($monitor->evaluate(11)); // 1st time for 11, count=1, false
        self::assertTrue($monitor->evaluate(11));  // 2nd time for 11, count=2, true
    }

    public function test_handles_bool_values(): void
    {
        $monitor = new StuckMonitor(2);

        self::assertFalse($monitor->evaluate(true));
        self::assertTrue($monitor->evaluate(true));

        // Switch to false
        self::assertFalse($monitor->evaluate(false));
        self::assertTrue($monitor->evaluate(false));
    }
}
