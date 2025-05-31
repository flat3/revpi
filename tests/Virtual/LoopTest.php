<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Virtual;

use Flat3\RevPi\Tests\TestCase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;
use Revolt\EventLoop;

class LoopTest extends TestCase implements UsesVirtualEnvironment
{
    public function test_loop(): void
    {
        $counter = 0;

        EventLoop::repeat(0, function () use (&$counter) {
            $counter++;
        });

        $this->loop(3);
        self::assertEquals(3, $counter);
    }
}
