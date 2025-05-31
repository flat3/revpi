<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort\Ioctl;

use Flat3\RevPi\Hardware\PosixDevice\Ioctl;

class SerialRS485 extends Ioctl
{
    public int $flags = 0;

    public int $delayRtsBeforeSend = 0;

    public int $delayRtsAfterSend = 0;

    /** @var int[] */
    public array $padding = [0, 0, 0, 0, 0];

    public const SER_RS485_TERMINATE_BUS = 1 << 5;

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
