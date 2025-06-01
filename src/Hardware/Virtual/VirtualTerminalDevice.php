<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Virtual;

use Flat3\RevPi\Contracts\TerminalDeviceInterface;
use Flat3\RevPi\Exceptions\IoctlFailedException;
use Flat3\RevPi\Hardware\SerialPort\Command;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\SerialRS485;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\Termios;

class VirtualTerminalDevice extends VirtualCharacterDevice implements TerminalDeviceInterface
{
    protected Termios $termios;

    protected SerialRS485 $serialRS485;

    public function __construct()
    {
        $this->termios = new Termios;
        $this->serialRS485 = new SerialRS485;
    }

    public function ioctl(int $request, ?string &$argp = null): int
    {
        if ($request === Command::TCGETS->value) {
            $argp = $this->termios->pack();

            return 0;
        }

        if ($request === Command::TCSETS->value) {
            assert($argp !== null);
            $this->termios->unpack($argp);

            return 0;
        }

        if ($request === Command::TIOCGRS485->value) {
            $argp = $this->serialRS485->pack();

            return 0;
        }

        if ($request === Command::TIOCSRS485->value) {
            assert($argp !== null);
            $this->serialRS485->unpack($argp);

            return 0;
        }

        throw new IoctlFailedException;
    }

    public function cfsetispeed(string &$buffer, int $speed): int
    {
        $message = new Termios;
        $message->unpack($buffer);
        $message->ispeed = $speed;
        $buffer = $message->pack();

        return 0;
    }

    public function cfsetospeed(string &$buffer, int $speed): int
    {
        $message = new Termios;
        $message->unpack($buffer);
        $message->ospeed = $speed;
        $buffer = $message->pack();

        return 0;
    }

    public function cfgetispeed(string &$buffer): int
    {
        $message = new Termios;
        $message->unpack($buffer);

        return $message->ispeed;
    }

    public function cfgetospeed(string &$buffer): int
    {
        $message = new Termios;
        $message->unpack($buffer);

        return $message->ospeed;
    }
}
