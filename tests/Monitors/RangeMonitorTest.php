<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Monitors\RangeMonitor;
use PHPUnit\Framework\TestCase;

class RangeMonitorTest extends TestCase
{
    public function test_within_range_returns_false(): void
    {
        $monitor = new RangeMonitor(10, 20);

        self::assertFalse($monitor->evaluate(10));
        self::assertFalse($monitor->evaluate(15));
        self::assertFalse($monitor->evaluate(20));
    }

    public function test_below_min_returns_true(): void
    {
        $monitor = new RangeMonitor(10, 20);

        self::assertTrue($monitor->evaluate(9));
        self::assertTrue($monitor->evaluate(-1));
    }

    public function test_above_max_returns_true(): void
    {
        $monitor = new RangeMonitor(10, 20);

        self::assertTrue($monitor->evaluate(21));
        self::assertTrue($monitor->evaluate(100));
    }

    public function test_bool_input(): void
    {
        $monitor = new RangeMonitor(0, 1);

        self::assertFalse($monitor->evaluate(true));  // true == 1
        self::assertFalse($monitor->evaluate(false)); // false == 0

        $monitor = new RangeMonitor(1, 1);

        self::assertTrue($monitor->evaluate(false));  // 0 < min==1

        $monitor = new RangeMonitor(0, 0);

        self::assertTrue($monitor->evaluate(true));   // 1 > max==0
    }

    public function test_edge_cases(): void
    {
        $monitor = new RangeMonitor(PHP_INT_MIN, PHP_INT_MAX);

        self::assertFalse($monitor->evaluate(0));
        self::assertFalse($monitor->evaluate(PHP_INT_MIN));
        self::assertFalse($monitor->evaluate(PHP_INT_MAX));
    }
}
