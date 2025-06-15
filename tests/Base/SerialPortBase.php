<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Base;

use Flat3\RevPi\Interfaces\SerialPort;
use Flat3\RevPi\SerialPort\BaudRate;
use Flat3\RevPi\SerialPort\ControlFlag;
use Flat3\RevPi\SerialPort\DataBits;
use Flat3\RevPi\SerialPort\InputFlag;
use Flat3\RevPi\SerialPort\LocalFlag;
use Flat3\RevPi\SerialPort\OutputFlag;
use Flat3\RevPi\SerialPort\Parity;
use Flat3\RevPi\SerialPort\QueueSelector;
use Flat3\RevPi\SerialPort\RS485Flag;
use Flat3\RevPi\SerialPort\StopBits;
use Flat3\RevPi\Tests\TestCase;

abstract class SerialPortBase extends TestCase
{
    public function test_flags(): void
    {
        $port = app(SerialPort::class);

        $flags = collect([
            ...RS485Flag::cases(),
            ...InputFlag::cases(),
            ...OutputFlag::cases(),
            ...ControlFlag::cases(),
            ...LocalFlag::cases(),
        ])->filter(fn ($flag) => ! in_array($flag, [
            RS485Flag::RtsOnSend,
            RS485Flag::RtsAfterSend,
            ControlFlag::EnableHardwareFlowControl,
        ], true));

        foreach ($flags as $flag) {
            $original = $port->getFlag($flag);
            $port->setFlag($flag);

            self::assertTrue($port->getFlag($flag), $flag->name);

            $port->clearFlag($flag);
            self::assertFalse($port->getFlag($flag), $flag->name);

            $original ? $port->setFlag($flag) : $port->clearFlag($flag);
        }
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

    public function test_drain(): void
    {
        self::expectNotToPerformAssertions();
        app(SerialPort::class)->drain();
    }

    public function test_flush(): void
    {
        self::expectNotToPerformAssertions();
        app(SerialPort::class)->flush(QueueSelector::Both);
    }

    public function test_break(): void
    {
        self::expectNotToPerformAssertions();
        app(SerialPort::class)->break(10);
    }
}
