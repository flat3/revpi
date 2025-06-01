<?php

declare(strict_types=1);

namespace Flat3\RevPi\SerialPort;

enum QueueSelector
{
    case Input;
    case Output;
    case Both;
}