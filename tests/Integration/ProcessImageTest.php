<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Integration;

use Flat3\RevPi\Interfaces\Modules\Connect5;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\ProcessImage\Device;
use Flat3\RevPi\ProcessImage\ModuleType;
use Flat3\RevPi\Tests\Base\ProcessImageBase;

class ProcessImageTest extends ProcessImageBase
{
    public function test_device_info(): void
    {
        $info = app(ProcessImage::class)->getDeviceInfo();

        self::assertEquals(135542, $info->serialNumber);
        self::assertEquals(ModuleType::KUNBUS_FW_DESCR_TYP_PI_CONNECT_5, $info->moduleType);
        self::assertEquals(0, $info->address);
    }

    public function test_device_info_list(): void
    {
        $info = app(ProcessImage::class)->getDeviceInfoList();

        self::assertEquals(6, $info->count());
        $device = $info->first();
        self::assertInstanceOf(Device::class, $device);
        self::assertEquals(135542, $device->serialNumber);
    }

    public function test_get_module(): void
    {
        $image = app(ProcessImage::class);
        self::assertInstanceOf(Connect5::class, $image->getModule());
    }
}
