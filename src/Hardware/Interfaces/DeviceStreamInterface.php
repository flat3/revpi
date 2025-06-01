<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Interfaces;

interface DeviceStreamInterface
{
    /** @return resource */
    public function fdopen(): mixed;
}
