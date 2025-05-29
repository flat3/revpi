<?php

namespace Flat3\RevPi\Hardware\ProcessImage;

enum ModuleState: int
{
    case Offline = 0;
    case Cyclic = 1;

    public function name(): string
    {
        return match ($this) {
            self::Offline => 'Offline',
            self::Cyclic => 'Online',
        };
    }
}
