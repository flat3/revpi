<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

interface Ioctl
{
    public function ioctl(int $request, ?string &$argp = null): int;
}
