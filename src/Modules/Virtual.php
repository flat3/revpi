<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Interfaces\Module as ModuleInterface;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Led\VirtualLed;

class Virtual extends Module implements ModuleInterface
{
    public function getLed(LedPosition $position): Led
    {
        return app(VirtualLed::class, ['position' => $position]);
    }
}
