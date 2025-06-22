<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

/**
 * Interface Stream
 *
 * Represents a hardware stream that can provide a file descriptor resource.
 */
interface Stream
{
    /**
     * Open or retrieve a file descriptor resource for the stream.
     *
     * @return resource The file descriptor resource associated with the stream.
     */
    public function fdopen(): mixed;
}
