<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Interfaces\Modules\Flat as FlatModuleInterface;
use Flat3\RevPi\Led\FlatLed;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;

class Flat extends Module implements FlatModuleInterface
{
    public function getLed(LedPosition $position): Led
    {
        return app(FlatLed::class, ['position' => $position]);
    }
}
