<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Virtual;

use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Modules\Virtual;
use Flat3\RevPi\ProcessImage\Device;
use Flat3\RevPi\ProcessImage\ModuleType;
use Flat3\RevPi\Tests\Base\ProcessImageBase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;

class ProcessImageTest extends ProcessImageBase implements UsesVirtualEnvironment
{
    public function test_device_info(): void
    {
        $info = app(ProcessImage::class)->getDeviceInfo();

        self::assertEquals(999999, $info->serialNumber);
        self::assertEquals(ModuleType::VIRTUAL, $info->moduleType);
        self::assertEquals(0, $info->address);
    }

    public function test_device_info_list(): void
    {
        $info = app(ProcessImage::class)->getDeviceInfoList();

        self::assertEquals(20, $info->count());
        $device = $info->first();
        self::assertInstanceOf(Device::class, $device);
        self::assertEquals(999999, $device->serialNumber);
    }

    public function test_get_module(): void
    {
        $image = app(ProcessImage::class);
        self::assertInstanceOf(Virtual::class, $image->getModule());
    }
}
