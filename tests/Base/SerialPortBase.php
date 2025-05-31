<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Base;

use Flat3\RevPi\Contracts\SerialPort;
use Flat3\RevPi\Hardware\SerialPort\BaudRate;
use Flat3\RevPi\Hardware\SerialPort\DataBits;
use Flat3\RevPi\Hardware\SerialPort\Parity;
use Flat3\RevPi\Hardware\SerialPort\StopBits;
use Flat3\RevPi\Tests\TestCase;

abstract class SerialPortBase extends TestCase
{
    public function test_termination(): void
    {
        $port = app(SerialPort::class);

        $t = $port->getTermination();

        $port->setTermination(false);
        self::assertFalse($port->getTermination());

        $port->setTermination(true);
        self::assertTrue($port->getTermination());

        $port->setTermination($t);
    }

    public function test_speed(): void
    {
        $port = app(SerialPort::class);

        $speed = $port->getSpeed();

        $port->setSpeed(BaudRate::B50);
        self::assertEquals(BaudRate::B50, $port->getSpeed());

        $port->setSpeed($speed);
    }

    public function test_parity(): void
    {
        $port = app(SerialPort::class);

        $parity = $port->getParity();

        $port->setParity(Parity::None);
        self::assertEquals(Parity::None, $port->getParity());

        $port->setParity(Parity::Even);
        self::assertEquals(Parity::Even, $port->getParity());

        $port->setParity(Parity::Odd);
        self::assertEquals(Parity::Odd, $port->getParity());

        $port->setParity($parity);
    }

    public function test_data_bits(): void
    {
        $port = app(SerialPort::class);

        $bits = $port->getDataBits();

        $port->setDataBits(DataBits::CS5);
        self::assertEquals(DataBits::CS5, $port->getDataBits());

        $port->setDataBits(DataBits::CS6);
        self::assertEquals(DataBits::CS6, $port->getDataBits());

        $port->setDataBits(DataBits::CS7);
        self::assertEquals(DataBits::CS7, $port->getDataBits());

        $port->setDataBits(DataBits::CS8);
        self::assertEquals(DataBits::CS8, $port->getDataBits());

        $port->setDataBits($bits);
    }

    public function test_stop_bits(): void
    {
        $port = app(SerialPort::class);

        $bits = $port->getStopBits();

        $port->setStopBits(StopBits::One);
        self::assertEquals(StopBits::One, $port->getStopBits());

        $port->setStopBits(StopBits::Two);
        self::assertEquals(StopBits::Two, $port->getStopBits());

        $port->setStopBits($bits);
    }
}
