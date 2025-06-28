<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

enum RateLimit
{
    case None;
    case Debounce;
    case Throttle;
}
