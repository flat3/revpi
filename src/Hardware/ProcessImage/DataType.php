<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage;

enum DataType: int
{
    case Bool = 1;
    case Byte = 8;
    case Word = 16;
    case DWord = 32;
}
