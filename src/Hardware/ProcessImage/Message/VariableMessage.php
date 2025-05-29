<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage\Message;

class VariableMessage extends Message
{
    public string $varName = '';

    public int $address = 0;

    public int $bit = 0;

    public int $length = 0;

    public function definition(): array
    {
        return [
            'varName' => 'Z32',
            'address' => 'v',
            'bit' => 'C',
            'x',
            'length' => 'v',
        ];
    }
}
