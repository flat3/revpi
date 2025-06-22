<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Integration;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Led\LedColour;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Monitors\DigitalMonitor;
use Flat3\RevPi\Tests\TestCase;

class Connect5Test extends TestCase
{
    protected bool $switch = false;

    public function test_led_get_set(): void
    {
        $module = app(Module::class);

        foreach (LedPosition::cases() as $position) {
            foreach (LedColour::cases() as $colour) {
                $module->getLed($position)->set($colour);
                self::assertEquals($colour, $module->getLed($position)->get());
            }

            $module->getLed($position)->off();
        }
    }

    public function test_analog_watch_high_low(): void
    {
        $module = app(Module::class);
        $module->getProcessImage()->writeVariable('AnalogOutputLogicLevel_1', false);
        $this->loop(2);

        $module->monitor('AnalogInputLogicLevel_1', new DigitalMonitor, function ($next) {
            $this->switch = $next;
        });

        $module->getProcessImage()->writeVariable('AnalogOutputLogicLevel_1', true);
        $this->loop(3);
        self::assertTrue($this->switch);

        $module->getProcessImage()->writeVariable('AnalogOutputLogicLevel_1', false);
        $this->loop(3);
        self::assertFalse($this->switch);
    }

    public function test_analog_voltage(): void
    {
        $module = app(Module::class);
        $module->getProcessImage()->writeVariable('AnalogOutput_3', 100);
        $this->loop(1, 100);
        $module->getProcessImage()->writeVariable('AnalogOutput_3', 0);
        $this->loop(1, 100);
        self::assertEquals(0, $module->getProcessImage()->readVariable('AnalogOutput_3'));
        self::assertEqualsWithDelta(0, $module->getProcessImage()->readVariable('AnalogInput_3'), 100);
        $module->getProcessImage()->writeVariable('AnalogOutput_3', 2000);
        $this->loop(1, 100);
        self::assertEqualsWithDelta(2000, $module->getProcessImage()->readVariable('AnalogInput_3'), 100);
    }

    public function test_digital_watch_high_low(): void
    {
        $module = app(Module::class);
        $module->getProcessImage()->writeVariable('DigitalOutput_3', false);
        self::assertFalse($module->getProcessImage()->readVariable('DigitalOutput_3'));

        $module->monitor('DigitalInput_4', new DigitalMonitor, function ($next) {
            $this->switch = $next;
        });

        $this->loop(1);

        $module->getProcessImage()->writeVariable('DigitalOutput_3', true);
        $this->loop(2);
        self::assertTrue($this->switch);

        $module->getProcessImage()->writeVariable('DigitalOutput_3', false);
        $this->loop(2);
        self::assertFalse($this->switch);
    }
}
