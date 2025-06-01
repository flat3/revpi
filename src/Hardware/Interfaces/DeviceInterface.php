<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Interfaces;

interface DeviceInterface
{
    public function open(string $pathname, int $flags): int;

    public function close(): int;

    public function read(string &$buffer, int $count): int;

    public function write(string $buffer, int $count): int;
}
