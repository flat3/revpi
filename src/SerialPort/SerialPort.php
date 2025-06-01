<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Exceptions\PosixDeviceException;
use Flat3\RevPi\Hardware\HasDeviceIoctl;
use Flat3\RevPi\Hardware\Interfaces\Terminal;
use Flat3\RevPi\Interfaces\SerialPort as SerialPortInterface;
use Flat3\RevPi\SerialPort\Ioctl\SerialRS485;
use Flat3\RevPi\SerialPort\Ioctl\Termios;
use Revolt\EventLoop;

class SerialPort implements SerialPortInterface
{
    use HasDeviceIoctl;

    /** @var resource */
    protected mixed $stream;

    public function __construct(protected Terminal $device, protected string $devicePath = '/dev/ttyRS485-0')
    {
        $device->open($this->devicePath, Constants::O_RDWR | Constants::O_NONBLOCK | Constants::O_NOCTTY);
        $this->stream = $this->device->fdopen();
    }

    public function getDevice(): Terminal
    {
        return $this->device;
    }

    public function onReadable(callable $callback): static
    {
        EventLoop::onReadable($this->stream, fn () => $callback(fread($this->stream, 1024)));

        return $this;
    }

    public function setSpeed(BaudRate $rate): static
    {
        $message = new Termios;
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
        $message = new Termios;
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
        $message = new Termios;
        $this->ioctl(Command::TCGETS, $message);
        $message->cflag &= ~(Termios::PARENB | Termios::PARODD);

        if ($parity === Parity::Odd) {
            $message->cflag |= Termios::PARODD | Termios::PARENB;
        }

        if ($parity === Parity::Even) {
            $message->cflag |= Termios::PARENB;
        }

        $this->ioctl(Command::TCSETS, $message);

        return $this;
    }

    public function getParity(): Parity
    {
        $message = new Termios;
        $this->ioctl(Command::TCGETS, $message);

        if (($message->cflag & Termios::PARENB) === 0) {
            return Parity::None;
        }

        if (($message->cflag & Termios::PARODD) !== 0) {
            return Parity::Odd;
        }

        return Parity::Even;
    }

    public function setDataBits(DataBits $bits): static
    {
        $message = new Termios;
        $this->ioctl(Command::TCGETS, $message);

        $message->cflag &= ~Termios::CSIZE;

        $message->cflag |= match ($bits) {
            DataBits::CS5 => Termios::CS5,
            DataBits::CS6 => Termios::CS6,
            DataBits::CS7 => Termios::CS7,
            DataBits::CS8 => Termios::CS8,
        };

        $this->ioctl(Command::TCSETS, $message);

        return $this;
    }

    public function getDataBits(): DataBits
    {
        $message = new Termios;
        $this->ioctl(Command::TCGETS, $message);

        return match ($message->cflag & Termios::CSIZE) {
            Termios::CS5 => DataBits::CS5,
            Termios::CS6 => DataBits::CS6,
            Termios::CS7 => DataBits::CS7,
            Termios::CS8 => DataBits::CS8,
            default => throw new NotImplementedException,
        };
    }

    public function setStopBits(StopBits $bits): static
    {
        $message = new Termios;
        $this->ioctl(Command::TCGETS, $message);

        if ($bits === StopBits::Two) {
            $message->cflag |= Termios::CSTOPB;
        } else {
            $message->cflag &= ~Termios::CSTOPB;
        }

        $this->ioctl(Command::TCSETS, $message);

        return $this;
    }

    public function getStopBits(): StopBits
    {
        $message = new Termios;
        $this->ioctl(Command::TCGETS, $message);

        return (($message->cflag & Termios::CSTOPB) !== 0) ? StopBits::Two : StopBits::One;
    }

    public function read(int $count = 1024): string
    {
        assert($count >= 1);
        $result = fread($this->stream, $count);

        if ($result === false) {
            throw new PosixDeviceException;
        }

        return $result;
    }

    public function write(string $data): void
    {
        fwrite($this->stream, $data);
    }
}
