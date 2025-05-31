<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Commands;

use Flat3\RevPi\Tests\TestCase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;
use Illuminate\Testing\PendingCommand;

class InfoTest extends TestCase implements UsesVirtualEnvironment
{
    public function test_info(): void
    {
        /** @var PendingCommand $test */
        $test = $this->artisan('revpi:info');

        $test->assertExitCode(0);
        $test->expectsOutputToContain('Virtual');
    }
}
