<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit;

use Flat3\RevPi\Contracts\ProcessImage;
use Flat3\RevPi\Exceptions\VariableNotFoundException;
use Flat3\RevPi\Hardware\ProcessImage\DataType;
use Flat3\RevPi\Hardware\ProcessImage\Device;
use Flat3\RevPi\Hardware\ProcessImage\ModuleType;
use Flat3\RevPi\Hardware\ProcessImage\Variable;
use Flat3\RevPi\Hardware\ProcessImage\VirtualPiControl;
use Flat3\RevPi\Hardware\Virtual;
use OverflowException;
use UnderflowException;

class ProcessImageTest extends UnitTestCase
{
    public function test_device_info(): void
    {
        $info = app(ProcessImage::class)->getDeviceInfo();

        $this->assertInstanceOf(Device::class, $info);
        $this->assertEquals(999999, $info->serialNumber);
        $this->assertEquals(ModuleType::VIRTUAL, $info->moduleType);
        $this->assertEquals(0, $info->address);
    }

    public function test_device_info_list(): void
    {
        $info = app(ProcessImage::class)->getDeviceInfoList();

        $this->assertEquals(20, $info->count());
        $device = $info->first();
        $this->assertInstanceOf(Device::class, $device);
        $this->assertEquals(999999, $device->serialNumber);
    }

    public function test_read_variable(): void
    {
        $image = app(ProcessImage::class);
        app(VirtualPiControl::class)->createVariable(new Variable('Test_0', DataType::Word));
        $this->assertEquals(0, $image->readVariable('Test_0'));
    }

    public function test_write_variable_exists(): void
    {
        $image = app(ProcessImage::class);
        app(VirtualPiControl::class)->createVariable(new Variable('Test_0', DataType::Word));
        $image->writeVariable('Test_0', 456);
        $this->assertEquals(456, $image->readVariable('Test_0'));
    }

    public function test_write_variable_not_found(): void
    {
        $image = app(ProcessImage::class);
        $this->expectException(VariableNotFoundException::class);
        $image->writeVariable('Test_1', 456);
    }

    public function test_read_not_found(): void
    {
        $image = app(ProcessImage::class);
        $this->expectException(VariableNotFoundException::class);
        $image->readVariable('Test_1');
    }

    public function test_dump_image(): void
    {
        $image = app(ProcessImage::class);
        $image->dumpImage();
        $this->assertEquals(4096, strlen($image->dumpImage()));
    }

    public function test_get_module(): void
    {
        $image = app(ProcessImage::class);
        $this->assertInstanceOf(Virtual::class, $image->getModule());
    }

    public function test_overflow(): void
    {
        $this->expectException(OverflowException::class);

        app(VirtualPiControl::class)->createVariable(new Variable('OutBit_1', DataType::Bool));
        app(ProcessImage::class)->writeVariable('OutBit_1', 4);
    }

    public function test_underflow(): void
    {
        $this->expectException(UnderflowException::class);

        app(VirtualPiControl::class)->createVariable(new Variable('OutBit_1', DataType::Bool));
        app(ProcessImage::class)->writeVariable('OutBit_1', -1);
    }

    public function test_ext_virtual_sizes(): void
    {
        $image = app(ProcessImage::class);

        $control = app(VirtualPiControl::class);

        foreach (range(1, 48) as $position) {
            $name = 'OutBit_'.$position;
            $control->createVariable(new Variable($name, DataType::Bool));

            foreach ([false, true] as $value) {
                $image->writeVariable($name, $value);
                $this->assertEquals($value, $image->readVariable($name));
            }

            $image->writeVariable($name, false);
        }

        foreach (range(1, 10) as $position) {
            $name = 'OutByte_'.$position;
            $control->createVariable(new Variable($name, DataType::Byte));

            foreach ([0, 1, pow(2, 8) - 1] as $value) {
                $image->writeVariable($name, $value);
                $this->assertEquals($value, $image->readVariable($name));
            }

            $image->writeVariable($name, 0);
        }

        foreach (range(1, 4) as $position) {
            $name = 'OutWord_'.$position;
            $control->createVariable(new Variable($name, DataType::Word));

            foreach ([0, 1, pow(2, 8) - 1, pow(2, 16) - 1] as $value) {
                $image->writeVariable($name, $value);
                $this->assertEquals($value, $image->readVariable($name));
            }

            $image->writeVariable($name, 0);
        }

        foreach (range(1, 2) as $position) {
            $name = 'OutDWord_'.$position;
            $control->createVariable(new Variable($name, DataType::DWord));

            foreach ([0, 1, pow(2, 8) - 1, pow(2, 16) - 1, pow(2, 32) - 1] as $value) {
                $image->writeVariable($name, $value);
                $this->assertEquals($value, $image->readVariable($name));
            }

            $image->writeVariable($name, 0);
        }
    }
}
