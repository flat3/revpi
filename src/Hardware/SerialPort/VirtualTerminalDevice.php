<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Contracts\TerminalDevice;
use Flat3\RevPi\Exceptions\IoctlFailedException;
use Flat3\RevPi\Exceptions\NotSupportedException;
use Flat3\RevPi\Exceptions\PosixDeviceException;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\SerialRS485;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\TermiosIoctl;

class VirtualTerminalDevice implements TerminalDevice
{
    protected TermiosIoctl $termios;

    protected SerialRS485 $serialRS485;

    /** @var resource */
    protected mixed $socket;

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

        $this->socket = $sockets[0];

        return $sockets[1];
    }

    public function open(string $pathname, int $flags): int
    {
        return 0;
    }

    public function close(int $fd): int
    {
        return 0;
    }

    public function read(int $fd, string &$buffer, int $count): int
    {
        if ($count === 0) {
            $buffer = '';

            return 0;
        }

        assert($count > 0);
        $result = fread($this->socket, $count);

        if ($result === false) {
            throw new PosixDeviceException;
        }

        $buffer = $result;

        return strlen($buffer);
    }

    public function write(int $fd, string $buffer, int $count): int
    {
        assert($count > 0);

        $result = fwrite($this->socket, $buffer, $count);

        if ($result === false) {
            throw new PosixDeviceException;
        }

        return $result;
    }

    public function lseek(int $fd, int $offset, int $whence): int
    {
        throw new NotSupportedException;
    }

    /**
     * @return resource
     */
    public function getSocket(): mixed
    {
        return $this->socket;
    }
}
