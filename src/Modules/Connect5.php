<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Interfaces\Modules\Connect5 as Connect5ModuleInterface;
use Flat3\RevPi\Led\Connect5Led;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;

class Connect5 extends Module implements Connect5ModuleInterface
{
    public function led(LedPosition $position): Led
    {
        return app(Connect5Led::class, ['position' => $position]);
    }
}
