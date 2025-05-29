<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit\Commands;

use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Hardware\Led\LedColour;
use Flat3\RevPi\Hardware\Led\LedPosition;
use Flat3\RevPi\Tests\Unit\UnitTestCase;
use Illuminate\Testing\PendingCommand;

class GetLedTest extends UnitTestCase
{
    public function test_info_off(): void
    {
        /** @var PendingCommand $test */
        $test = $this->artisan('revpi:led:get a1');

        $test->assertExitCode(0);
        $test->expectsOutputToContain('Off');
    }

    public function test_info_on(): void
    {
        app(BaseModule::class)->led(LedPosition::A1)->set(LedColour::Blue);

        /** @var PendingCommand $test */
        $test = $this->artisan('revpi:led:get a1');

        $test->assertExitCode(0);
        $test->expectsOutputToContain('Blue');
    }
}
