<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Interfaces;

interface PosixSeekInterface
{
    public function lseek(int $offset, int $whence): int;
}
