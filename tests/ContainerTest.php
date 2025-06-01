<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\Modules\Compact;
use Flat3\RevPi\Interfaces\Modules\Connect5;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Modules\Virtual;

class ContainerTest extends TestCase implements UsesVirtualEnvironment
{
    public function test_get_connect5_implementation(): void
    {
        $base = app(Connect5::class);
        self::assertInstanceOf(\Flat3\RevPi\Modules\Connect5::class, $base);
    }

    public function test_get_compact_implementation(): void
    {
        $base = app(Compact::class);
        self::assertInstanceOf(\Flat3\RevPi\Modules\Compact::class, $base);
    }

    public function test_get_any_implementation(): void
    {
        $base = app(Module::class);
        self::assertInstanceOf(Virtual::class, $base);
    }

    public function test_get_process_image(): void
    {
        $base = app(ProcessImage::class);
        self::assertInstanceOf(\Flat3\RevPi\ProcessImage\ProcessImage::class, $base);
    }
}
