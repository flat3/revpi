<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Interfaces;

interface TerminalInterface extends DeviceInterface, IoctlInterface, StreamInterface
{
    public function cfgetispeed(string &$buffer): int;

    public function cfgetospeed(string &$buffer): int;

    public function cfsetispeed(string &$buffer, int $speed): int;

    public function cfsetospeed(string &$buffer, int $speed): int;
}
