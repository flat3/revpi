<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Exceptions\PosixDeviceException;
use Flat3\RevPi\Hardware\HasDeviceIoctl;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\Interfaces\SerialPort as SerialPortInterface;
use Flat3\RevPi\SerialPort\Ioctl\SerialRS485Struct;
use Flat3\RevPi\SerialPort\Ioctl\TermiosStruct;
use Revolt\EventLoop;

class SerialPort implements SerialPortInterface
{
    use HasDeviceIoctl;

    /** @var resource */
    protected mixed $stream;

    public function __construct(protected Terminal $device, protected string $devicePath = '/dev/ttyRS485')
    {
        $device->open($this->devicePath, Constants::O_RDWR | Constants::O_NONBLOCK | Constants::O_NOCTTY);
        $this->stream = $this->device->fdopen();
    }

    public function getDevice(): Terminal
    {
        return $this->device;
    }

    public function onReadable(callable $callback): string
    {
        return EventLoop::onReadable($this->stream, function () use ($callback) {
            $callback($this);
        });
    }

    public function setSpeed(BaudRate $rate): static
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);
        $buffer = $message->pack();
        $this->device->cfsetispeed($buffer, $rate->value);
        $this->device->cfsetospeed($buffer, $rate->value);
        $message->unpack($buffer);
        $this->ioctl(Command::TerminalControlSet, $message);

        return $this;
    }

    public function getSpeed(): BaudRate
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);
        $buffer = $message->pack();
        $speed = $this->device->cfgetispeed($buffer);

        return BaudRate::from($speed);
    }

    public function setParity(Parity $parity): static
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);
        $message->cflag &= ~(TermiosStruct::PARENB | TermiosStruct::PARODD);

        if ($parity === Parity::Odd) {
            $message->cflag |= TermiosStruct::PARODD | TermiosStruct::PARENB;
        }

        if ($parity === Parity::Even) {
            $message->cflag |= TermiosStruct::PARENB;
        }

        $this->ioctl(Command::TerminalControlSet, $message);

        return $this;
    }

    public function getParity(): Parity
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);

        if (($message->cflag & TermiosStruct::PARENB) === 0) {
            return Parity::None;
        }

        if (($message->cflag & TermiosStruct::PARODD) !== 0) {
            return Parity::Odd;
        }

        return Parity::Even;
    }

    public function setDataBits(DataBits $bits): static
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);

        $message->cflag &= ~TermiosStruct::CSIZE;
        $message->cflag |= $bits->value;

        $this->ioctl(Command::TerminalControlSet, $message);

        return $this;
    }

    public function getDataBits(): DataBits
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);

        return DataBits::from($message->cflag & TermiosStruct::CSIZE);
    }

    public function setStopBits(StopBits $bits): static
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);

        if ($bits === StopBits::Two) {
            $message->cflag |= TermiosStruct::CSTOPB;
        } else {
            $message->cflag &= ~TermiosStruct::CSTOPB;
        }

        $this->ioctl(Command::TerminalControlSet, $message);

        return $this;
    }

    public function getStopBits(): StopBits
    {
        $message = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $message);

        return (($message->cflag & TermiosStruct::CSTOPB) !== 0) ? StopBits::Two : StopBits::One;
    }

    public function setFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): static
    {
        if ($flag instanceof RS485Flag) {
            $rs485 = new SerialRS485Struct;
            $this->ioctl(Command::TerminalControlGetRS485, $rs485);
            $rs485->flags |= $flag->value;
            $this->ioctl(Command::TerminalControlSetRS485, $rs485);

            return $this;
        }

        $termios = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $termios);

        switch (true) {
            case $flag instanceof InputFlag:
                $termios->iflag |= $flag->value;
                break;

            case $flag instanceof OutputFlag:
                $termios->oflag |= $flag->value;
                break;

            case $flag instanceof ControlFlag:
                $termios->cflag |= $flag->value;
                break;

            case $flag instanceof LocalFlag: // @phpstan-ignore instanceof.alwaysTrue
                $termios->lflag |= $flag->value;
                break;
        }

        $this->ioctl(Command::TerminalControlSet, $termios);

        return $this;
    }

    public function clearFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): static
    {
        if ($flag instanceof RS485Flag) {
            $rs485 = new SerialRS485Struct;
            $this->ioctl(Command::TerminalControlGetRS485, $rs485);
            $rs485->flags &= ~$flag->value;
            $this->ioctl(Command::TerminalControlSetRS485, $rs485);

            return $this;
        }

        $termios = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $termios);

        switch (true) {
            case $flag instanceof InputFlag:
                $termios->iflag &= ~$flag->value;
                break;

            case $flag instanceof OutputFlag:
                $termios->oflag &= ~$flag->value;
                break;

            case $flag instanceof ControlFlag:
                $termios->cflag &= ~$flag->value;
                break;

            case $flag instanceof LocalFlag: // @phpstan-ignore instanceof.alwaysTrue
                $termios->lflag &= ~$flag->value;
                break;
        }

        $this->ioctl(Command::TerminalControlSet, $termios);

        return $this;
    }

    public function getFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): bool
    {
        if ($flag instanceof RS485Flag) {
            $rs485 = new SerialRS485Struct;
            $this->ioctl(Command::TerminalControlGetRS485, $rs485);

            return (bool) ($rs485->flags & $flag->value);
        }

        $termios = new TermiosStruct;
        $this->ioctl(Command::TerminalControlGet, $termios);

        return match (true) {
            $flag instanceof InputFlag => (bool) ($termios->iflag & $flag->value),
            $flag instanceof OutputFlag => (bool) ($termios->oflag & $flag->value),
            $flag instanceof ControlFlag => (bool) ($termios->cflag & $flag->value),
            $flag instanceof LocalFlag => (bool) ($termios->lflag & $flag->value),
        };
    }

    public function read(int $count = 1024): string
    {
        assert($count >= 1);
        $result = @fread($this->stream, $count);

        if ($result === false) {
            throw new PosixDeviceException;
        }

        return $result;
    }

    public function write(string $data): void
    {
        fwrite($this->stream, $data);
    }

    public function flush(QueueSelector $selector = QueueSelector::Both): void
    {
        $result = $this->device->tcflush(match ($selector) {
            QueueSelector::Input => 0,
            QueueSelector::Output => 1,
            QueueSelector::Both => 2,
        });

        if ($result !== 0) {
            throw new PosixDeviceException;
        }
    }

    public function drain(): void
    {
        $result = $this->device->tcdrain();

        if ($result !== 0) {
            throw new PosixDeviceException;
        }
    }

    public function break(int $duration = 0): void
    {
        $result = $this->device->tcsendbreak($duration);

        if ($result !== 0) {
            throw new PosixDeviceException;
        }
    }
}
