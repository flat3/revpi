<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Interfaces;

interface DeviceIoctlInterface
{
    public function ioctl(int $request, ?string &$argp = null): int;
}
