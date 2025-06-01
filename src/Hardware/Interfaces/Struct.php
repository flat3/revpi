<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Interfaces;

interface Struct
{
    public function pack(): string;

    public function unpack(string $buffer): void;

    public function length(): int;
}
