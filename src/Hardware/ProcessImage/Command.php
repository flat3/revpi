<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage;

use Flat3\RevPi\Hardware\PosixDevice\IoctlCommand;

enum Command: int implements IoctlCommand
{
    public const Magic = 75 << 8;

    case Reset = self::Magic | 12;
    case GetDeviceInfoList = self::Magic | 13;

    case GetDeviceInfo = self::Magic | 14;
    case GetValue = self::Magic | 15;
    case SetValue = self::Magic | 16;

    case FindVariable = self::Magic | 17;
}
