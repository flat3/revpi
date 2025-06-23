<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Hardware\Virtual\VirtualTerminalDevice;
use Flat3\RevPi\Interfaces\Module as ModuleInterface;
use Flat3\RevPi\Interfaces\SerialPort;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Led\VirtualLed;

class Virtual extends Module implements ModuleInterface
{
    public function getLed(LedPosition $position): Led
    {
        return app(VirtualLed::class, ['position' => $position]);
    }

    public function getSerialPort(string $devicePath = '/dev/ttyRS485'): SerialPort
    {
        return app(SerialPort::class, ['device' => app(VirtualTerminalDevice::class)]);
    }
}
