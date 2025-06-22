<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

/**
 * Interface Seek
 *
 * Provides an interface for seeking within a hardware context, such as moving a file pointer
 * or device resource to a specific position.
 */
interface Seek
{
    /**
     * Set the position of the resource pointer.
     *
     * Moves the pointer to a specific offset, relative to the position defined by $whence.
     *
     * @param  int  $offset  The position to move to, measured in bytes.
     * @param  int  $whence  The reference position for the offset; one of SEEK_SET, SEEK_CUR, or SEEK_END.
     * @return int The new position of the pointer on success.
     */
    public function lseek(int $offset, int $whence): int;
}
