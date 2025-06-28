<?php

namespace Flat3\RevPi\Tests\Monitors;

use Flat3\RevPi\Monitors\DigitalMonitor;
use Flat3\RevPi\Tests\TestCase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;

class MonitorTest extends TestCase implements UsesVirtualEnvironment
{
    /**
     * Helper to advance the event loop n times (simulate time passing).
     */
    public function test_no_throttle_debounce(): void
    {
        $monitor = new DigitalMonitor;
        $results = [];

        $callback = function ($val) use (&$results) {
            $results[] = $val;
        };

        $wrapped = $monitor->wrap($callback);

        // Simulate variable changes
        $inputs = [0, 0, 1, 1, 0, 1, 1, 0];
        foreach ($inputs as $i => $value) {
            if ($monitor->evaluate($value)) {
                $wrapped($value);
            }
            $this->loop(1, 2);
        }

        // Only trigger callback on CHANGE (not same value twice),
        // DigitalMonitor returns true for a transition.
        self::assertSame([1, 0, 1, 0], $results);
    }

    public function test_fast_debounce(): void
    {
        $monitor = (new DigitalMonitor)->debounce(1); // 1ms debounce
        $results = [];
        $callback = function ($val) use (&$results) {
            $results[] = $val;
        };
        $wrapped = $monitor->wrap($callback);

        $inputs = [0, 1, 0, 1, 0];
        foreach ($inputs as $value) {
            if ($monitor->evaluate($value)) {
                $wrapped($value);
            }
            $this->loop(1, 2);
        }

        // Advance loop to fire debounce once
        $this->loop(2, 2);

        self::assertSame([0], $results);

        // Debounce with enough time between events so each triggers
        $monitor = (new DigitalMonitor)->debounce(1);
        $results = [];
        $wrapped = $monitor->wrap(function ($v) use (&$results) {
            $results[] = $v;
        });

        $transitions = [0, 1, 0, 1, 0];
        foreach ($transitions as $value) {
            if ($monitor->evaluate($value)) {
                $wrapped($value);
            }
            $this->loop(2, 2); // Let debounce interval expire
        }
        self::assertSame([1, 0, 1, 0], $results);
    }

    public function test_fast_throttle(): void
    {
        $monitor = (new DigitalMonitor)->throttle(2); // 2ms throttle
        $results = [];
        $callback = function ($val) use (&$results) {
            $results[] = $val;
        };
        $wrapped = $monitor->wrap($callback);

        $inputs = [0, 1, 0, 1, 0];

        // Submit all transitions rapidly with 0ms gap, before throttle can expire
        foreach ($inputs as $value) {
            if ($monitor->evaluate($value)) {
                $wrapped($value);
            }
        }
        // Should only fire the first one!
        $this->loop(3, 2);

        // Now let throttle window expire, and send another transition
        if ($monitor->evaluate(1)) {
            $wrapped(1);
        }
        $this->loop(3, 2);

        self::assertSame([1, 1], $results);
    }
}
