<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Integration;

use Flat3\RevPi\Contracts\TerminalDevice;
use Flat3\RevPi\Hardware\SerialPort\Command;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\TermiosIoctl;
use Flat3\RevPi\Tests\Unit\UnitTestCase;

class SerialPortTest extends UnitTestCase
{
    public function test_serial_port(): void
    {
        $port = app(TerminalDevice::class);
        $message = new TermiosIoctl;
        $fd = $port->open('/dev/ttyRS485-0', 2);
        $message->cc[0] = 42;
        $message->cc[1] = 43;
        $buffer = $message->pack();
        $port->ioctl($fd, Command::TCGets->value, $buffer);
        echo base64_encode($buffer);
        assert(is_string($buffer));
        $message->unpack($buffer);
    }
}
