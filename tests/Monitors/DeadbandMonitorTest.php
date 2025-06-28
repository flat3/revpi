<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Exceptions\NotSupportedException;
use Flat3\RevPi\Monitors\DeadbandMonitor;
use PHPUnit\Framework\TestCase;

class DeadbandMonitorTest extends TestCase
{
    public function test_initially_inside_deadband_does_not_trigger(): void
    {
        $monitor = new DeadbandMonitor(10.0, 2.0); // Center: 10, Deadband: 2
        self::assertFalse($monitor->evaluate(11)); // |11-10| = 1 < 2, inside
    }

    public function test_moves_outside_deadband_triggers_once(): void
    {
        $monitor = new DeadbandMonitor(10.0, 2.0);

        // Inside deadband
        $monitor->evaluate(10);

        // Moves outside
        self::assertTrue($monitor->evaluate(15)); // |15-10| = 5 > 2, outside

        // Still outside, should not trigger again
        self::assertFalse($monitor->evaluate(13));
    }

    public function test_returning_inside_deadband_does_not_trigger(): void
    {
        $monitor = new DeadbandMonitor(10.0, 2.0);

        $monitor->evaluate(15); // go outside

        self::assertFalse($monitor->evaluate(10)); // |10-10| = 0 < 2, back inside
        self::assertFalse($monitor->evaluate(12)); // Still inside
    }

    public function test_trigger_on_each_crossing_outside(): void
    {
        $monitor = new DeadbandMonitor(10.0, 2.0);

        $monitor->evaluate(10); // inside

        // Cross outside
        self::assertTrue($monitor->evaluate(15));

        // Still outside
        self::assertFalse($monitor->evaluate(20));

        // Back inside
        $monitor->evaluate(11);

        // Cross outside again
        self::assertTrue($monitor->evaluate(7));   // |7-10| = 3 > 2 → outside
    }

    public function test_bool_input(): void
    {
        $monitor = new DeadbandMonitor(1.0, 0.5);

        $this->expectException(NotSupportedException::class);
        $monitor->evaluate(true);
    }
}
