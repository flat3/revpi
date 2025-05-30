<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Contracts\SerialPort as SerialPortContract;
use Flat3\RevPi\Contracts\TerminalDevice as TerminalContract;

class SerialPort implements SerialPortContract
{
    protected int $fd;

    public function __construct(protected TerminalContract $port, protected string $device = '/dev/ttyRS485-0')
    {
        $this->fd = $port->open($this->device, 2);
    }

    public function configure(PortConfiguration $configuration): void
    {
        $buffer = $configuration->toMessage()->pack();
        $this->port->ioctl($this->fd, Command::TCSets->value, $buffer);
    }
}