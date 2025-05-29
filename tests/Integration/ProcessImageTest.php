<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Integration;

use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Exceptions\OverflowException;
use Flat3\RevPi\Exceptions\UnderflowException;
use Flat3\RevPi\Tests\TestCase;

class ProcessImageTest extends TestCase
{
    public function test_overflow(): void
    {
        self::expectException(OverflowException::class);

        $module = app(BaseModule::class);
        $image = $module->image();
        $image->writeVariable('OutBit_1', 4);
    }

    public function test_underflow(): void
    {
        self::expectException(UnderflowException::class);

        $module = app(BaseModule::class);
        $image = $module->image();
        $image->writeVariable('OutBit_1', -1);
    }

    public function test_ext_virtual_sizes(): void
    {
        $module = app(BaseModule::class);
        $image = $module->image();

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
