<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

class DigitalTrigger extends Trigger
{
    public function evaluate(mixed $previous, mixed $next): void
    {
        call_user_func($this->callback, $next);
    }
}
