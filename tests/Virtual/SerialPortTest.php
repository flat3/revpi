<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Virtual;

use Flat3\RevPi\Contracts\SerialPort;
use Flat3\RevPi\Hardware\SerialPort\BaudRate;
use Flat3\RevPi\Hardware\SerialPort\VirtualTerminalDevice;
use Flat3\RevPi\Tests\Base\SerialPortBase;
use Flat3\RevPi\Tests\UsesVirtualEnvironment;

class SerialPortTest extends SerialPortBase implements UsesVirtualEnvironment
{
    public function test_read(): void
    {
        $capture = '';

        app(SerialPort::class)
            ->setSpeed(BaudRate::B9600)
            ->setTermination(false)
            ->onReadable(function ($text) use (&$capture) {
                $capture .= $text;
            });

        $device = app(VirtualTerminalDevice::class);

        fwrite($device->getSocket(), 'Hello, world!');

        $this->loop(1);

        self::assertEquals('Hello, world!', $capture);
    }
}
