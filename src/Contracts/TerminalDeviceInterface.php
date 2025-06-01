<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\Interfaces\DeviceIoctlInterface;
use Flat3\RevPi\Hardware\Interfaces\DeviceStreamInterface;
use Flat3\RevPi\Hardware\Interfaces\PosixDeviceInterface;

interface TerminalDeviceInterface extends DeviceIoctlInterface, DeviceStreamInterface, PosixDeviceInterface
{
    public function cfgetispeed(string &$buffer): int;

    public function cfgetospeed(string &$buffer): int;

    public function cfsetispeed(string &$buffer, int $speed): int;

    public function cfsetospeed(string &$buffer, int $speed): int;
}
