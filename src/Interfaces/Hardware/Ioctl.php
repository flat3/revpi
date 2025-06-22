<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

/**
 * Interface Ioctl
 *
 * Hardware abstraction for devices supporting ioctl operations.
 */
interface Ioctl
{
    /**
     * Perform an ioctl operation on the device.
     *
     * @param  int  $request  The ioctl request code.
     * @param  string|null  $argp  Optional; additional argument for the ioctl operation, passed by reference.
     * @return int The result code from the ioctl system call.
     */
    public function ioctl(int $request, ?string &$argp = null): int;
}
