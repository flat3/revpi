<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Integration;

use Flat3\RevPi\Contracts\SerialPort;
use Flat3\RevPi\Hardware\SerialPort\BaudRate;
use Flat3\RevPi\Hardware\SerialPort\Parity;
use Flat3\RevPi\Tests\TestCase;

class SerialPortTest extends TestCase
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
}
