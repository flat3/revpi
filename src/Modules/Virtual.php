<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Hardware\Virtual\VirtualLed;
use Flat3\RevPi\Interfaces\Module as ModuleInterface;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;

class Virtual extends Module implements ModuleInterface
{
    public function led(LedPosition $position): Led
    {
        return app(VirtualLed::class, ['position' => $position]);
    }
}
