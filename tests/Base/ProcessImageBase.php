<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Base;

use Flat3\RevPi\Exceptions\OverflowException;
use Flat3\RevPi\Exceptions\UnderflowException;
use Flat3\RevPi\Exceptions\VariableNotFoundException;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Tests\TestCase;

abstract class ProcessImageBase extends TestCase
{
    public function test_overflow(): void
    {
        $this->expectException(OverflowException::class);
        app(ProcessImage::class)->writeVariable('OutBit_1', 4);
    }

    public function test_underflow(): void
    {
        $this->expectException(UnderflowException::class);
        app(ProcessImage::class)->writeVariable('OutBit_1', -1);
    }

    public function test_dump_image(): void
    {
        $image = app(ProcessImage::class);
        self::assertEquals(4096, strlen($image->dumpImage()));
    }

    public function test_read_variable(): void
    {
        $image = app(ProcessImage::class);
        $image->writeVariable('OutByte_1', 123);
        self::assertEquals(123, $image->readVariable('OutByte_1'));
    }

    public function test_write_variable_exists(): void
    {
        $image = app(ProcessImage::class);
        $image->writeVariable('OutByte_1', 4);
        self::assertEquals(4, $image->readVariable('OutByte_1'));
    }

    public function test_write_variable_not_found(): void
    {
        $image = app(ProcessImage::class);
        $this->expectException(VariableNotFoundException::class);
        $image->writeVariable('Missing_1', 4);
    }

    public function test_read_not_found(): void
    {
        $image = app(ProcessImage::class);
        $this->expectException(VariableNotFoundException::class);
        $image->readVariable('Missing_1');
    }

    public function test_sizes(): void
    {
        $module = app(Module::class);
        $image = $module->getProcessImage();

        foreach (range(1, 48) as $position) {
            foreach ([false, true] as $value) {
                $image->writeVariable('OutBit_'.$position, $value);
                self::assertEquals($value, $image->readVariable('OutBit_'.$position));
            }

            $image->writeVariable('OutBit_'.$position, false);
        }

        foreach (range(1, 10) as $position) {
            foreach ([0, 1, pow(2, 8) - 1] as $value) {
                $image->writeVariable('OutByte_'.$position, $value);
                self::assertEquals($value, $image->readVariable('OutByte_'.$position));
            }

            $image->writeVariable('OutByte_'.$position, 0);
        }

        foreach (range(1, 4) as $position) {
            foreach ([0, 1, pow(2, 8) - 1, pow(2, 16) - 1] as $value) {
                $image->writeVariable('OutWord_'.$position, $value);
                self::assertEquals($value, $image->readVariable('OutWord_'.$position));
            }

            $image->writeVariable('OutWord_'.$position, 0);
        }

        foreach (range(1, 2) as $position) {
            foreach ([0, 1, pow(2, 8) - 1, pow(2, 16) - 1, pow(2, 32) - 1] as $value) {
                $image->writeVariable('OutDWord_'.$position, $value);
                self::assertEquals($value, $image->readVariable('OutDWord_'.$position));
            }

            $image->writeVariable('OutDWord_'.$position, 0);
        }
    }
}
