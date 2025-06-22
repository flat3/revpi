<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

/**
 * Interface Device
 *
 * Defines the basic contract for a hardware device, allowing it to be opened, closed,
 * and support reading and writing data.
 */
interface Device
{
    /**
     * Open a connection to the device specified by its pathname.
     *
     * @param  string  $pathname  The device file or resource path.
     * @param  int  $flags  Flags to control how the device is opened.
     * @return int Returns a resource handle or file descriptor on success, or -1 on failure.
     */
    public function open(string $pathname, int $flags): int;

    /**
     * Close the connection to the device.
     *
     * @return int Returns 0 on success or -1 on failure.
     */
    public function close(): int;

    /**
     * Read from the device into the provided buffer.
     *
     * @param  string  $buffer  Variable to store the read data (passed by reference).
     * @param  int  $count  Maximum number of bytes to read.
     * @return int The number of bytes read, or -1 on error.
     */
    public function read(string &$buffer, int $count): int;

    /**
     * Write data to the device.
     *
     * @param  string  $buffer  The data to write.
     * @param  int  $count  Number of bytes to write.
     * @return int The number of bytes written, or -1 on error.
     */
    public function write(string $buffer, int $count): int;
}
