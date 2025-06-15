<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

enum DataBits: int
{
    /**
     * 5 data bits per character.
     */
    case CS5 = 0000000; // CS5

    /**
     * 6 data bits per character.
     */
    case CS6 = 0000020; // CS6

    /**
     * 7 data bits per character.
     */
    case CS7 = 0000040; // CS7

    /**
     * 8 data bits per character (most common).
     */
    case CS8 = 0000060; // CS8
}
