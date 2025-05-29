<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit\Commands;

use Flat3\RevPi\Tests\Unit\UnitTestCase;
use Illuminate\Testing\PendingCommand;

class DumpTest extends UnitTestCase
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
