<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests;

use Flat3\RevPi;
use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\Modules\Compact;
use Flat3\RevPi\Interfaces\Modules\Connect5;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Modules\Virtual;

class ContainerTest extends TestCase implements UsesVirtualEnvironment
{
    public function test_get_connect5_implementation(): void
    {
        self::assertInstanceOf(RevPi\Modules\Connect5::class, app(Connect5::class));
    }

    public function test_get_compact_implementation(): void
    {
        self::assertInstanceOf(RevPi\Modules\Compact::class, app(Compact::class));
    }

    public function test_get_any_implementation(): void
    {
        self::assertInstanceOf(Virtual::class, app(Module::class));
    }

    public function test_get_process_image(): void
    {
        self::assertInstanceOf(RevPi\ProcessImage\ProcessImage::class, app(ProcessImage::class));
    }

    public function test_picontrol(): void
    {
        self::assertInstanceOf(PiControl::class, app(PiControl::class)); // @phpstan-ignore staticMethod.alreadyNarrowedType
    }

    public function test_terminal(): void
    {
        self::assertInstanceOf(Terminal::class, app(Terminal::class)); // @phpstan-ignore staticMethod.alreadyNarrowedType
    }
}
