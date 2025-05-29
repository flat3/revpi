<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit;

use Revolt\EventLoop;

class LoopTest extends UnitTestCase
{
    public function test_loop(): void
    {
        $counter = 0;

        EventLoop::repeat(0, function () use (&$counter) {
            $counter++;
        });

        $this->loop(3);
        $this->assertEquals(3, $counter);
    }
}
