<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

use Flat3\RevPi\Interfaces\Hardware\IoctlCommand;

enum Command: int implements IoctlCommand
{
    public const Magic = 0x5400;

    case TerminalControlGet = Command::Magic | 0x01;
    case TerminalControlSet = Command::Magic | 0x02;

    case TerminalControlGetRS485 = Command::Magic | 0x2E;
    case TerminalControlSetRS485 = Command::Magic | 0x2F;
}
