<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Monitors\DigitalMonitor;
use PHPUnit\Framework\TestCase;

class DigitalMonitorTest extends TestCase
{
    public function test_first_value_always_returns_false(): void
    {
        $monitor = new DigitalMonitor;

        self::assertFalse($monitor->evaluate(1));
        self::assertFalse((new DigitalMonitor)->evaluate(true));
        self::assertFalse((new DigitalMonitor)->evaluate(0));
        self::assertFalse((new DigitalMonitor)->evaluate(false));
    }

    public function test_same_value_twice_returns_false(): void
    {
        $monitor = new DigitalMonitor;

        $monitor->evaluate(1);
        self::assertFalse($monitor->evaluate(1));

        $monitor = new DigitalMonitor;
        $monitor->evaluate(false);
        self::assertFalse($monitor->evaluate(false));
    }

    public function test_different_value_returns_true(): void
    {
        $monitor = new DigitalMonitor;

        $monitor->evaluate(1);
        self::assertTrue($monitor->evaluate(0));

        $monitor = new DigitalMonitor;
        $monitor->evaluate(false);
        self::assertTrue($monitor->evaluate(true));

        $monitor = new DigitalMonitor;
        $monitor->evaluate(0);
        self::assertTrue($monitor->evaluate(1));
    }

    public function test_alternating_values(): void
    {
        $monitor = new DigitalMonitor;

        $monitor->evaluate(0);
        self::assertTrue($monitor->evaluate(1));
        self::assertTrue($monitor->evaluate(0));
        self::assertTrue($monitor->evaluate(true));
        self::assertTrue($monitor->evaluate(false));
    }
}
