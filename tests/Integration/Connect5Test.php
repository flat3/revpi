<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Integration;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Led\LedColour;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Monitors\DigitalTrigger;
use Flat3\RevPi\Tests\TestCase;

class Connect5Test extends TestCase
{
    protected bool $switch = false;

    public function test_led_get_set(): void
    {
        $module = app(Module::class);

        foreach (LedPosition::cases() as $position) {
            foreach (LedColour::cases() as $colour) {
                $module->led($position)->set($colour);
                self::assertEquals($colour, $module->led($position)->get());
            }

            $module->led($position)->off();
        }
    }

    public function test_analog_watch_high_low(): void
    {
        $module = app(Module::class);
        $module->image()->writeVariable('AnalogOutputLogicLevel_1', false);
        $this->loop(2);

        $module->monitor(new DigitalTrigger('AnalogInputLogicLevel_1', function ($next) {
            $this->switch = $next;
        }));

        $module->image()->writeVariable('AnalogOutputLogicLevel_1', true);
        $this->loop(3);
        self::assertTrue($this->switch);

        $module->image()->writeVariable('AnalogOutputLogicLevel_1', false);
        $this->loop(3);
        self::assertFalse($this->switch);
    }

    public function test_analog_voltage(): void
    {
        $module = app(Module::class);
        $module->image()->writeVariable('AnalogOutput_3', 100);
        $this->loop(1, 100);
        $module->image()->writeVariable('AnalogOutput_3', 0);
        $this->loop(1, 100);
        self::assertEquals(0, $module->image()->readVariable('AnalogOutput_3'));
        self::assertEqualsWithDelta(0, $module->image()->readVariable('AnalogInput_3'), 100);
        $module->image()->writeVariable('AnalogOutput_3', 2000);
        $this->loop(1, 100);
        self::assertEqualsWithDelta(2000, $module->image()->readVariable('AnalogInput_3'), 100);
    }

    public function test_digital_watch_high_low(): void
    {
        $module = app(Module::class);
        $module->image()->writeVariable('DigitalOutput_3', false);
        self::assertFalse($module->image()->readVariable('DigitalOutput_3'));

        $module->monitor(new DigitalTrigger('DigitalInput_4', function ($next) {
            $this->switch = $next;
        }));

        $this->loop(1);

        $module->image()->writeVariable('DigitalOutput_3', true);
        $this->loop(2);
        self::assertTrue($this->switch);

        $module->image()->writeVariable('DigitalOutput_3', false);
        $this->loop(2);
        self::assertFalse($this->switch);
    }
}
