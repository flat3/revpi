<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Contracts\TerminalDevice;
use Flat3\RevPi\Hardware\PosixDevice\VirtualPosixDevice;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\TermiosIoctl;

class VirtualTerminalDevice extends VirtualPosixDevice implements TerminalDevice
{
    protected TermiosIoctl $termios;

    public function __construct()
    {
        $this->termios = new TermiosIoctl;
    }

    public function ioctl(int $fd, int $request, ?string &$argp = null): int
    {
        if ($request === Command::TCGets->value) {
            $argp = $this->termios->pack();
        }

        if ($request === Command::TCSets->value) {
            $this->termios->unpack($argp);
        }

        return 0;
    }
}
