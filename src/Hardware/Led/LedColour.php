<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Led;

enum LedColour
{
    case Off;
    case Red;
    case Green;
    case Orange;
    case Blue;
    case Magenta;
    case Cyan;

    case White;
}
