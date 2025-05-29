<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit;

use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Contracts\Compact;
use Flat3\RevPi\Exceptions\HardwareNotFoundException;
use Flat3\RevPi\Hardware\Led\LedColour;
use Flat3\RevPi\Hardware\Led\LedPosition;

class LedTest extends UnitTestCase
{
    public function test_led_not_found(): void
    {
        $this->app->bind(BaseModule::class, Compact::class);
        $this->expectException(HardwareNotFoundException::class);
        app(BaseModule::class)->led(LedPosition::A3)->set(LedColour::Blue);
    }

    public function test_led_get_set(): void
    {
        $led = app(BaseModule::class)->led(LedPosition::A3);
        $led->set(LedColour::Blue);
        self::assertEquals(LedColour::Blue, $led->get());
    }
}
