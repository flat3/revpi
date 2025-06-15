<?php

declare(strict_types=1);

namespace Flat3\RevPi\ProcessImage;

use Flat3\RevPi\Interfaces\Hardware\IoctlCommand;

enum Command: int implements IoctlCommand
{
    public const Magic = 0x4B00;

    case Reset = self::Magic | 0x0C;
    case GetDeviceInfoList = self::Magic | 0x0D;

    case GetDeviceInfo = self::Magic | 0x0E;
    case GetValue = self::Magic | 0x0F;
    case SetValue = self::Magic | 0x10;

    case FindVariable = self::Magic | 0x11;
}
