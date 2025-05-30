<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit;

use Flat3\RevPi\Contracts\SerialPort;
use Flat3\RevPi\Hardware\SerialPort\Ioctl\TermiosIoctl;
use Flat3\RevPi\Hardware\SerialPort\PortConfiguration;

class SerialPortTest extends UnitTestCase
{
    public function test_serial_port(): void
    {
        $buffer = base64_decode(
            'AAUAAAUAAAC9DAAAO4oAAAADHH8VBAABABETGgASDxcWAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
            true
        );

        $message = new TermiosIoctl;
        $message->unpack($buffer);
        $configuration = PortConfiguration::fromMessage($message);

        $port = app(SerialPort::class);
        $port->configure($configuration);
    }
}
