<?php

declare(strict_types=1);

namespace Flat3\RevPi\Led;

use Flat3\RevPi\Exceptions\HardwareNotFoundException;

class Connect4Led extends Led
{
    protected function position(LedPosition|int $position): int|LedPosition
    {
        return match ($position) {
            0 => LedPosition::A1,
            LedPosition::A1 => 0,
            3 => LedPosition::A2,
            LedPosition::A2 => 3,
            6 => LedPosition::A3,
            LedPosition::A3 => 6,
            9 => LedPosition::A4,
            LedPosition::A4 => 9,
            12 => LedPosition::A5,
            LedPosition::A5 => 12,
            default => throw new HardwareNotFoundException,
        };
    }

    protected function value(LedColour|int $colour): int|LedColour
    {
        return match ($colour) {
            0 => LedColour::Off,
            LedColour::Off => 0,
            1 => LedColour::Red,
            LedColour::Red => 1,
            2 => LedColour::Green,
            LedColour::Green => 2,
            3 => LedColour::Blue,
            LedColour::Blue => 3,
            4 => LedColour::Orange,
            LedColour::Orange => 4,
            5 => LedColour::Cyan,
            LedColour::Cyan => 5,
            6 => LedColour::Magenta,
            LedColour::Magenta => 6,
            7 => LedColour::White,
            LedColour::White => 7,
            default => throw new HardwareNotFoundException,
        };
    }
}
