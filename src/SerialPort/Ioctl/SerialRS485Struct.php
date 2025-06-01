<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort\Ioctl;

use Flat3\RevPi\Hardware\Struct;

class SerialRS485Struct extends Struct
{
    public int $flags = 0;

    public int $delayRtsBeforeSend = 0;

    public int $delayRtsAfterSend = 0;

    /** @var int[] */
    public array $padding = [0, 0, 0, 0, 0];

    public function definition(): array
    {
        return [
            'flags' => 'L',
            'delayRtsBeforeSend' => 'L',
            'delayRtsAfterSend' => 'L',
            'padding' => 'L5',
        ];
    }
}
