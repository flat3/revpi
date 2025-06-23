<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Interfaces\Modules\Connect4 as Connect4ModuleInterface;
use Flat3\RevPi\Led\Connect4Led;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;

class Connect4 extends Module implements Connect4ModuleInterface
{
    public function getLed(LedPosition $position): Led
    {
        return app(Connect4Led::class, ['position' => $position]);
    }
}
