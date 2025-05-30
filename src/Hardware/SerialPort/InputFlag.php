<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

enum InputFlag: int
{
    case IgnoreBreakCondition = 0x00000001;
    case SignalInterruptOnBreak = 0x00000002;
    case IgnoreParityErrors = 0x00000004;
    case MarkParityErrors = 0x00000008;
    case EnableInputParityChecking = 0x00000010;
    case StripHighBit = 0x00000020;
    case MapNLtoCR = 0x00000040;
    case IgnoreCR = 0x00000080;
    case MapCRtoNL = 0x00000100;
    case MapUppercaseToLowercase = 0x00000200;
    case EnableXonXoffOutputFlowControl = 0x00000400;
    case AnyCharRestartsOutput = 0x00000800;
    case EnableXonXoffInputFlowControl = 0x00001000;
    case RingBellOnInputQueueFull = 0x00002000;
    case InputIsUtf8 = 0x00004000;
}