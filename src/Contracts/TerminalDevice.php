<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\PosixDevice\PosixDevice;

interface TerminalDevice extends PosixDevice
{
    public function cfgetispeed(string &$buffer): int;

    public function cfgetospeed(string &$buffer): int;

    public function cfsetispeed(string &$buffer, int $speed): int;

    public function cfsetospeed(string &$buffer, int $speed): int;

    /**
     * @return resource
     */
    public function stream_open(int $fd): mixed;
}
