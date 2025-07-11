<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort\Ioctl;

use Flat3\RevPi\Hardware\Struct;

class TermiosStruct extends Struct
{
    public int $iflag = 0;

    public int $oflag = 0;

    public int $cflag = 0;

    public int $lflag = 0;

    public int $line = 0;

    /** @var int[] */
    public array $cc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    public int $ispeed = 0;

    public int $ospeed = 0;

    public const CSIZE = 0000060;

    public const CSTOPB = 0000100;

    public const PARENB = 0000400;

    public const PARODD = 0001000;

    public function definition(): array
    {
        return [
            'iflag' => 'L',
            'oflag' => 'L',
            'cflag' => 'L',
            'lflag' => 'L',
            'line' => 'C',
            'cc' => 'C32',
            'ispeed' => 'L',
            'ospeed' => 'L',
            'x3',
        ];
    }
}
