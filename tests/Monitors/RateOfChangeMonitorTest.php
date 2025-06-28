<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Exceptions\NotSupportedException;
use Flat3\RevPi\Monitors\RateOfChangeMonitor;
use PHPUnit\Framework\TestCase;

class RateOfChangeMonitorTest extends TestCase
{
    public function test_first_evaluation_returns_false(): void
    {
        $monitor = new RateOfChangeMonitor(5.0); // max rate 5

        self::assertFalse($monitor->evaluate(10));
    }

    public function test_rate_within_threshold_returns_false(): void
    {
        $monitor = new RateOfChangeMonitor(5.0);

        $monitor->evaluate(10); // sets previous

        self::assertFalse($monitor->evaluate(13)); // |13-10| = 3 < 5
    }

    public function test_rate_exceeds_threshold_returns_true(): void
    {
        $monitor = new RateOfChangeMonitor(2.0);

        $monitor->evaluate(10); // sets previous

        self::assertTrue($monitor->evaluate(20)); // |20-10| = 10 > 2
    }

    public function test_boolean_input_works(): void
    {
        $monitor = new RateOfChangeMonitor(1.0);

        $this->expectException(NotSupportedException::class);

        $monitor->evaluate(true);
    }

    public function test_zero_threshold_throws(): void
    {
        $this->expectException(NotSupportedException::class);

        new RateOfChangeMonitor(0.0);
    }

    public function test_negative_threshold_throws(): void
    {
        $this->expectException(NotSupportedException::class);

        new RateOfChangeMonitor(-5);
    }

    public function test_negative_to_positive(): void
    {
        $monitor = new RateOfChangeMonitor(5.0);

        $monitor->evaluate(-10);

        self::assertTrue($monitor->evaluate(0)); // |0 - (-10)| = 10 > 5
    }

    public function test_multiple_evaluations(): void
    {
        $monitor = new RateOfChangeMonitor(3.0);

        self::assertFalse($monitor->evaluate(1));  // first, always false
        self::assertFalse($monitor->evaluate(3));  // |3 - 1| = 2 < 3
        self::assertTrue($monitor->evaluate(10));  // |10 - 3| = 7 > 3
        self::assertFalse($monitor->evaluate(11)); // |11 - 10| = 1 < 3
    }
}
