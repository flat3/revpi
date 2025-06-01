<?php

declare(strict_types=1);

namespace Flat3\RevPi\Led;

use Flat3\RevPi\Exceptions\HardwareNotFoundException;

class CoreSLed extends Led
{
    protected function position(LedPosition|int $position): int|LedPosition
    {
        return match ($position) {
            0 => LedPosition::A1,
            LedPosition::A1 => 0,
            2 => LedPosition::A2,
            LedPosition::A2 => 2,
            default => throw new HardwareNotFoundException,
        };
    }

    protected function value(LedColour|int $colour): int|LedColour
    {
        return match ($colour) {
            0 => LedColour::Off,
            LedColour::Off => 0,
            1 => LedColour::Green,
            LedColour::Green => 1,
            2 => LedColour::Red,
            LedColour::Red => 2,
            3 => LedColour::Orange,
            LedColour::Orange => 3,
            default => throw new HardwareNotFoundException,
        };
    }
}
