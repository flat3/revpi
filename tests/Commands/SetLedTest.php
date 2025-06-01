<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Commands;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Led\LedColour;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Tests\TestCase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;
use Illuminate\Testing\PendingCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class SetLedTest extends TestCase implements UsesVirtualEnvironment
{
    public function test_set(): void
    {
        /** @var PendingCommand $test */
        $test = $this->artisan('revpi:led:set a1 cyan');
        $test->doesntExpectOutput();
        $test->assertExitCode(0);
        $test->run();

        self::assertEquals(LedColour::Cyan, app(Module::class)->led(LedPosition::A1)->get());
    }

    public function test_set_invalid(): void
    {
        /** @var PendingCommand $test */
        $test = $this->artisan('revpi:led:set a1 black');
        $test->doesntExpectOutput();
        $test->assertExitCode(0);

        $this->expectException(InvalidArgumentException::class);
    }
}
