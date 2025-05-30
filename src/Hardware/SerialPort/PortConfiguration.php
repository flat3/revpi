<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use Flat3\RevPi\Hardware\SerialPort\Ioctl\TermiosIoctl;
use Illuminate\Support\Collection;

class PortConfiguration
{
    /**
     * @var Collection<int, InputFlag>
     */
    public Collection $inputFlags;

    /**
     * @var Collection<int, OutputFlag>
     */
    public Collection $outputFlags;

    public int $inputRate = 0;

    public int $outputRate = 0;

    protected const rates = [
        0 => 0,
        1 => 50,
        2 => 75,
        3 => 110,
        4 => 134,
        5 => 150,
        6 => 200,
        7 => 300,
        8 => 600,
        9 => 1200,
        10 => 1800,
        11 => 2400,
        12 => 4800,
        13 => 9600,
        14 => 19200,
        15 => 38400,
        4097 => 57600,
        4098 => 115200,
        4099 => 230400,
        4100 => 460800,
        4101 => 500000,
        4102 => 576000,
        4103 => 921600,
        4104 => 1000000,
        4105 => 1152000,
        4106 => 1500000,
        4107 => 2000000,
        4108 => 2500000,
        4109 => 3000000,
        4110 => 3500000,
        4111 => 4000000,
    ];

    public function __construct()
    {
        $this->inputFlags = collect();
        $this->outputFlags = collect();
    }

    public static function fromMessage(TermiosIoctl $message): self
    {
        $c = new self;

        $c->inputFlags = collect(InputFlag::cases())
            ->filter(fn(InputFlag $flag) => ($message->iflag & $flag->value) === $flag->value)
            ->values();

        $c->outputFlags = collect(OutputFlag::cases())
            ->filter(fn(OutputFlag $flag) => ($message->oflag & $flag->value) === $flag->value)
            ->values();

        $c->inputRate = self::rates[$message->ispeed];
        $c->outputRate = self::rates[$message->ospeed];

        return $c;
    }

    public function toMessage(): TermiosIoctl
    {
        $m = new TermiosIoctl;
        $m->ispeed = array_flip(self::rates)[$this->inputRate];
        $m->ospeed = array_flip(self::rates)[$this->outputRate];
        $m->iflag = (int) $this->inputFlags->reduce(fn(int $value, InputFlag $flag) => $value | $flag->value, 0);
        $m->oflag = (int) $this->outputFlags->reduce(fn(int $value, OutputFlag $flag) => $value | $flag->value, 0);

        return $m;
    }
}
