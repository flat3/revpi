<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Commands;

use Flat3\RevPi\Tests\TestCase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;
use Illuminate\Testing\PendingCommand;

class DumpTest extends TestCase implements UsesVirtualEnvironment
{
    public function test_dump(): void
    {
        /** @var PendingCommand $test */
        $test = $this->artisan('revpi:dump test.file');
        $test->doesntExpectOutput();
        $test->assertExitCode(0);
        $test->run();
        self::assertFileExists('test.file');
        unlink('test.file');
    }
}
