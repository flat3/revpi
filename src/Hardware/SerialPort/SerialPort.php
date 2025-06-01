<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Contracts\SerialPort as SerialPortContract;
use Flat3\RevPi\Contracts\TerminalDevice as TerminalContract;
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Hardware\PosixDevice\HasIoctl;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\SerialRS485;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\TermiosIoctl;
use Revolt\EventLoop;

class SerialPort implements SerialPortContract
{
    use HasIoctl;

    protected int $fd;

    public function __construct(protected TerminalContract $device, protected string $devicePath = '/dev/ttyRS485-0')
    {
        $this->fd = $device->open($this->devicePath, Constants::O_RDWR | Constants::O_NONBLOCK | Constants::O_NOCTTY);
    }

    public function __destruct()
    {
        $this->device->close($this->fd);
    }

    public function onReadable(callable $callback): static
    {
        $stream = $this->device->stream($this->fd);
        EventLoop::onReadable($stream, fn() => $callback(fread($stream, 1024)));

        return $this;
    }

    public function setSpeed(BaudRate $rate): static
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);
        $buffer = $message->pack();
        $this->device->cfsetispeed($buffer, $rate->value);
        $this->device->cfsetospeed($buffer, $rate->value);
        $message->unpack($buffer);
        $this->ioctl(Command::TCSETS, $message);

        return $this;
    }

    public function getSpeed(): BaudRate
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);
        $buffer = $message->pack();
        $speed = $this->device->cfgetispeed($buffer);

        return BaudRate::from($speed);
    }

    public function setTermination(bool $enabled): static
    {
        $rs485Conf = new SerialRS485;
        $this->ioctl(Command::TIOCGRS485, $rs485Conf);

        if ($enabled) {
            $rs485Conf->flags |= SerialRS485::SER_RS485_TERMINATE_BUS;
        } else {
            $rs485Conf->flags &= ~SerialRS485::SER_RS485_TERMINATE_BUS;
        }

        $this->ioctl(Command::TIOCSRS485, $rs485Conf);

        return $this;
    }

    public function getTermination(): bool
    {
        $rs485Conf = new SerialRS485;
        $this->ioctl(Command::TIOCGRS485, $rs485Conf);

        return (bool) ($rs485Conf->flags & SerialRS485::SER_RS485_TERMINATE_BUS);
    }

    public function setParity(Parity $parity): static
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);
        $message->cflag &= ~(TermiosIoctl::PARENB | TermiosIoctl::PARODD);

        if ($parity === Parity::Odd) {
            $message->cflag |= TermiosIoctl::PARODD | TermiosIoctl::PARENB;
        }

        if ($parity === Parity::Even) {
            $message->cflag |= TermiosIoctl::PARENB;
        }

        $this->ioctl(Command::TCSETS, $message);

        return $this;
    }

    public function getParity(): Parity
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);

        if (($message->cflag & TermiosIoctl::PARENB) === 0) {
            return Parity::None;
        }

        if (($message->cflag & TermiosIoctl::PARODD) !== 0) {
            return Parity::Odd;
        }

        return Parity::Even;
    }

    public function setDataBits(DataBits $bits): static
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);

        $message->cflag &= ~TermiosIoctl::CSIZE;

        $message->cflag |= match ($bits) {
            DataBits::CS5 => TermiosIoctl::CS5,
            DataBits::CS6 => TermiosIoctl::CS6,
            DataBits::CS7 => TermiosIoctl::CS7,
            DataBits::CS8 => TermiosIoctl::CS8,
        };

        $this->ioctl(Command::TCSETS, $message);

        return $this;
    }

    public function getDataBits(): DataBits
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);

        return match ($message->cflag & TermiosIoctl::CSIZE) {
            TermiosIoctl::CS5 => DataBits::CS5,
            TermiosIoctl::CS6 => DataBits::CS6,
            TermiosIoctl::CS7 => DataBits::CS7,
            TermiosIoctl::CS8 => DataBits::CS8,
            default => throw new NotImplementedException,
        };
    }

    public function setStopBits(StopBits $bits): static
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);

        if ($bits === StopBits::Two) {
            $message->cflag |= TermiosIoctl::CSTOPB;
        } else {
            $message->cflag &= ~TermiosIoctl::CSTOPB;
        }

        $this->ioctl(Command::TCSETS, $message);

        return $this;
    }

    public function getStopBits(): StopBits
    {
        $message = new TermiosIoctl;
        $this->ioctl(Command::TCGETS, $message);

        return (($message->cflag & TermiosIoctl::CSTOPB) !== 0) ? StopBits::Two : StopBits::One;
    }
}
