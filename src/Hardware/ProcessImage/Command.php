<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage;

enum Command: int
{
    public const Magic = 75 << 8;

    case Reset = Command::Magic | 12;
    case GetDeviceInfoList = Command::Magic | 13;

    case GetDeviceInfo = Command::Magic | 14;
    case GetValue = Command::Magic | 15;
    case SetValue = Command::Magic | 16;

    case FindVariable = Command::Magic | 17;
}
