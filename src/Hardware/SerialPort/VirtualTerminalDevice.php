<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Contracts\TerminalDevice;
use Flat3\RevPi\Exceptions\IoctlFailedException;
use Flat3\RevPi\Hardware\PosixDevice\VirtualPosixDevice;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\SerialRS485;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\TermiosIoctl;

class VirtualTerminalDevice extends VirtualPosixDevice implements TerminalDevice
{
    protected TermiosIoctl $termios;

    protected SerialRS485 $serialRS485;

    public function __construct()
    {
        $this->termios = new TermiosIoctl;
        $this->serialRS485 = new SerialRS485;
    }

    public function ioctl(int $fd, int $request, ?string &$argp = null): int
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

    public function stream_open(int $fd): mixed
    {
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

        assert($sockets !== false);

        return $sockets[0];
    }
}
