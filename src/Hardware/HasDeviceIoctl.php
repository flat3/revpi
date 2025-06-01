<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware;

use Flat3\RevPi\Exceptions\IoctlFailedException;
use Flat3\RevPi\Hardware\Interfaces\IoctlCommand;
use Flat3\RevPi\Hardware\Interfaces\Struct;

trait HasDeviceIoctl
{
    protected function ioctl(IoctlCommand $command, ?Struct $message = null): int
    {
        assert(is_int($command->value));

        if ($message === null) {
            $ret = $this->device->ioctl($command->value);

            if ($ret < 0) {
                throw new IoctlFailedException;
            }

            return $ret;
        }

        $buf = $message->pack();
        $ret = $this->device->ioctl($command->value, $buf);

        if ($ret < 0) {
            throw new IoctlFailedException;
        }

        assert(is_string($buf));

        $message->unpack($buf);

        return $ret;
    }
}
