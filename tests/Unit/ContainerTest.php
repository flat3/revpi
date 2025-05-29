<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit;

use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Contracts\Compact;
use Flat3\RevPi\Contracts\Connect5;
use Flat3\RevPi\Contracts\ProcessImage;
use Flat3\RevPi\Hardware\Compact as CompactHardware;
use Flat3\RevPi\Hardware\Connect5 as Connect5Hardware;
use Flat3\RevPi\Hardware\Virtual;

class ContainerTest extends UnitTestCase
{
    public function test_get_connect5_implementation(): void
    {
        $base = app(Connect5::class);
        $this->assertInstanceOf(Connect5Hardware::class, $base);
        $this->assertInstanceOf(BaseModule::class, $base);
        $this->assertInstanceOf(Connect5::class, $base);
    }

    public function test_get_compact_implementation(): void
    {
        $base = app(Compact::class);
        $this->assertInstanceOf(CompactHardware::class, $base);
        $this->assertInstanceOf(BaseModule::class, $base);
        $this->assertInstanceOf(Compact::class, $base);
    }

    public function test_get_any_implementation(): void
    {
        $base = app(BaseModule::class);
        $this->assertInstanceOf(BaseModule::class, $base);
        $this->assertInstanceOf(Virtual::class, $base);
    }

    public function test_get_process_image(): void
    {
        $base = app(ProcessImage::class);
        $this->assertInstanceOf(ProcessImage::class, $base);
    }
}
