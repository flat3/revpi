<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Virtual;

use Flat3\RevPi\Exceptions\IoctlFailedException;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\SerialPort\Command;
use Flat3\RevPi\SerialPort\Ioctl\SerialRS485Struct;
use Flat3\RevPi\SerialPort\Ioctl\TermiosStruct;

class VirtualTerminalDevice extends VirtualCharacterDevice implements Terminal
{
    protected TermiosStruct $termios;

    protected SerialRS485Struct $serialRS485;

    public function __construct()
    {
        $this->termios = new TermiosStruct;
        $this->serialRS485 = new SerialRS485Struct;
    }

    public function ioctl(int $request, ?string &$argp = null): int
    {
        if ($request === Command::TerminalControlGet->value) {
            $argp = $this->termios->pack();

            return 0;
        }

        if ($request === Command::TerminalControlSet->value) {
            assert($argp !== null);
            $this->termios->unpack($argp);

            return 0;
        }

        if ($request === Command::TerminalControlGetRS485->value) {
            $argp = $this->serialRS485->pack();

            return 0;
        }

        if ($request === Command::TerminalControlSetRS485->value) {
            assert($argp !== null);
            $this->serialRS485->unpack($argp);

            return 0;
        }

        throw new IoctlFailedException;
    }

    public function cfsetispeed(string &$buffer, int $speed): int
    {
        $message = new TermiosStruct;
        $message->unpack($buffer);
        $message->ispeed = $speed;
        $buffer = $message->pack();

        return 0;
    }

    public function cfsetospeed(string &$buffer, int $speed): int
    {
        $message = new TermiosStruct;
        $message->unpack($buffer);
        $message->ospeed = $speed;
        $buffer = $message->pack();

        return 0;
    }

    public function cfgetispeed(string &$buffer): int
    {
        $message = new TermiosStruct;
        $message->unpack($buffer);

        return $message->ispeed;
    }

    public function cfgetospeed(string &$buffer): int
    {
        $message = new TermiosStruct;
        $message->unpack($buffer);

        return $message->ospeed;
    }

    public function tcflush(int $queue_selector): int
    {
        return 0;
    }

    public function tcdrain(): int
    {
        return 0;
    }

    public function tcsendbreak(int $duration = 0): int
    {
        return 0;
    }
}
