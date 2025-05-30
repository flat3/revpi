<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

enum Command: int
{
    public const Magic = 0x5400;

    case TCGets = Command::Magic | 1;
    case TCSets = Command::Magic | 2;
}
