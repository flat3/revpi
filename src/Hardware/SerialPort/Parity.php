<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

enum Parity
{
    case None;
    case Odd;
    case Even;
}
