<?php

declare(strict_types=1);

namespace Flat3\RevPi\Led;

use Flat3\RevPi\Exceptions\HardwareNotFoundException;

class FlatSLed extends Led
{
    protected function position(LedPosition|int $position): LedPosition|int
    {
        return match ($position) {
            0 => LedPosition::A1,
            LedPosition::A1 => 0,
            2 => LedPosition::A2,
            LedPosition::A2 => 2,
            4 => LedPosition::A3,
            LedPosition::A3 => 4,
            6 => LedPosition::A4,
            LedPosition::A4 => 6,
            8 => LedPosition::A5,
            LedPosition::A5 => 8,
            default => throw new HardwareNotFoundException,
        };
    }

    protected function value(LedColour|int $colour): LedColour|int
    {
        return match ($colour) {
            0 => LedColour::Off,
            LedColour::Off => 0,
            1 => LedColour::Blue,
            LedColour::Red => 1,
            2 => LedColour::Green,
            LedColour::Green => 2,
            3 => LedColour::Orange,
            LedColour::Orange => 3,
            default => throw new HardwareNotFoundException,
        };
    }
}
