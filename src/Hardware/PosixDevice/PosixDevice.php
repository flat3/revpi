<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\PosixDevice;

interface PosixDevice
{
    public function open(string $pathname, int $flags): int;

    public function close(int $fd): int;

    public function read(int $fd, string &$buffer, int $count): int;

    public function write(int $fd, string $buffer, int $count): int;

    public function lseek(int $fd, int $offset, int $whence): int;

    public function ioctl(int $fd, int $request, ?string &$argp = null): int;
}
