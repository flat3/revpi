<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Contracts\TerminalDevice as TerminalIOContract;
use Flat3\RevPi\Hardware\PosixDevice\HardwarePosixDevice;

class TerminalDevice extends HardwarePosixDevice implements TerminalIOContract
{
}