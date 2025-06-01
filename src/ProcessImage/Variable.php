<?php

declare(strict_types=1);

namespace Flat3\RevPi\ProcessImage;

final class Variable
{
    public int $address;

    public function __construct(
        public string $name,
        public DataType $type,
    ) {}
}
