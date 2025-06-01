<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Interfaces;

interface Terminal extends Device, Ioctl, Stream
{
    public function cfgetispeed(string &$buffer): int;

    public function cfgetospeed(string &$buffer): int;

    public function cfsetispeed(string &$buffer, int $speed): int;

    public function cfsetospeed(string &$buffer, int $speed): int;

    public function tcflush(int $queue_selector): int;

    public function tcdrain(): int;

    public function tcsendbreak(int $duration = 0): int;
}
