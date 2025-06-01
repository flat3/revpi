<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Hardware\Interfaces\IoctlCommand;

enum Command: int implements IoctlCommand
{
    public const Magic = 0x5400;

    case TCGETS = Command::Magic | 1;
    case TCSETS = Command::Magic | 2;

    case TIOCSRS485 = 0x542F;
    case TIOCGRS485 = 0x542E;
}
