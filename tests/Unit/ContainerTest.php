<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit;

use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Contracts\Compact;
use Flat3\RevPi\Contracts\Connect5;
use Flat3\RevPi\Contracts\ProcessImage;
use Flat3\RevPi\Hardware;

class ContainerTest extends UnitTestCase
{
    public function test_get_connect5_implementation(): void
    {
        $base = app(Connect5::class);
        self::assertInstanceOf(Hardware\Connect5::class, $base);
    }

    public function test_get_compact_implementation(): void
    {
        $base = app(Compact::class);
        self::assertInstanceOf(Hardware\Compact::class, $base);
    }

    public function test_get_any_implementation(): void
    {
        $base = app(BaseModule::class);
        self::assertInstanceOf(Hardware\Virtual::class, $base);
    }

    public function test_get_process_image(): void
    {
        $base = app(ProcessImage::class);
        self::assertInstanceOf(Hardware\ProcessImage\ProcessImage::class, $base);
    }
}
