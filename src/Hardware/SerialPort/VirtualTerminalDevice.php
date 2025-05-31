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
        if ($request === Command::TCGETS->value) {
            $argp = $this->termios->pack();
        }

        if ($request === Command::TCSETS->value) {
            assert($argp !== null);
            $this->termios->unpack($argp);
        }

        return 0;
    }

    public function cfsetispeed(string &$buffer, int $speed): int
    {
        $message = new TermiosIoctl;
        $message->unpack($buffer);
        $message->ispeed = $speed;
        $buffer = $message->pack();

        return 0;
    }

    public function cfsetospeed(string &$buffer, int $speed): int
    {
        $message = new TermiosIoctl;
        $message->unpack($buffer);
        $message->ospeed = $speed;
        $buffer = $message->pack();

        return 0;
    }

    public function cfgetispeed(string &$buffer): int
    {
        $message = new TermiosIoctl;
        $message->unpack($buffer);

        return $message->ispeed;
    }

    public function cfgetospeed(string &$buffer): int
    {
        $message = new TermiosIoctl;
        $message->unpack($buffer);

        return $message->ospeed;
    }
}
