<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\PosixDevice;

use Flat3\RevPi\Exceptions\IoctlFailedException;

trait HasIoctl
{
    protected function ioctl(IoctlCommand $command, ?IoctlContract $message = null): int
    {
        assert(is_int($command->value));

        if ($message === null) {
            $ret = $this->device->ioctl($this->fd, $command->value);

            if ($ret < 0) {
                throw new IoctlFailedException;
            }

            return $ret;
        }

        $buf = $message->pack();
        $ret = $this->device->ioctl($this->fd, $command->value, $buf);

        if ($ret < 0) {
            throw new IoctlFailedException;
        }

        assert(is_string($buf));

        $message->unpack($buf);

        return $ret;
    }
}
