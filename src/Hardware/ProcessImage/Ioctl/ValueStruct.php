<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage\Ioctl;

use Flat3\RevPi\Hardware\Interop\Struct;

class ValueStruct extends Struct
{
    public int $address = 0;

    public int $bit = 0;

    public int $value = 0;

    public function definition(): array
    {
        return [
            'address' => 'v',
            'bit' => 'C',
            'value' => 'C',
        ];
    }
}
