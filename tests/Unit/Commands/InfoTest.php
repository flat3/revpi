<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit\Commands;

use Flat3\RevPi\Tests\Unit\UnitTestCase;
use Illuminate\Testing\PendingCommand;

class InfoTest extends UnitTestCase
{
    public function test_info(): void
    {
        /** @var PendingCommand $test */
        $test = $this->artisan('revpi:info');

        $test->assertExitCode(0);
        $test->expectsOutputToContain('Virtual');
    }
}
