<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Virtual;

use Flat3\RevPi\Exceptions\HardwareNotFoundException;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\Modules\Compact;
use Flat3\RevPi\Led\LedColour;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Tests\TestCase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;

class LedTest extends TestCase implements UsesVirtualEnvironment
{
    public function test_led_not_found(): void
    {
        $this->app->bind(Module::class, Compact::class);
        $this->expectException(HardwareNotFoundException::class);
        app(Module::class)->led(LedPosition::A3)->set(LedColour::Blue);
    }

    public function test_led_get_set(): void
    {
        $led = app(Module::class)->led(LedPosition::A3);
        $led->set(LedColour::Blue);
        self::assertEquals(LedColour::Blue, $led->get());
    }
}
