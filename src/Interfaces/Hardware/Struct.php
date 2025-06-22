<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

/**
 * Interface Struct
 *
 * Interface for packable and unpackable binary structure representations.
 */
interface Struct
{
    /**
     * Packs the current data structure into a binary string.
     *
     * @return string The packed binary representation of this structure.
     */
    public function pack(): string;

    /**
     * Unpacks the given binary buffer into this data structure.
     *
     * @param  string  $buffer  The binary buffer to unpack into this structure.
     */
    public function unpack(string $buffer): void;

    /**
     * Returns the length in bytes of the packed data structure.
     *
     * @return int The length in bytes.
     */
    public function length(): int;
}
