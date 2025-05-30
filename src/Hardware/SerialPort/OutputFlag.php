<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

enum OutputFlag: int
{
    case EnableOutputPostProcessing = 0x00000001;
    case MapLowercaseToUppercaseOnOutput = 0x00000002;
    case MapNLtoCRNLOnOutput = 0x00000004;
    case MapCRToNLOnOutput = 0x00000008;
    case NoCROutputAtColumnZero = 0x00000010;
    case NoCROutput = 0x00000020;
    case FillZeroInsteadOfNullOnOutput = 0x00000040;
    case UseDeleteCharForFill = 0x00000080;
}