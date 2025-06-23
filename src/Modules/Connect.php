<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Interfaces\Modules\Connect as ConnectModuleInterface;
use Flat3\RevPi\Led\ConnectLed;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;

class Connect extends Module implements ConnectModuleInterface
{
    public function getLed(LedPosition $position): Led
    {
        return app(ConnectLed::class, ['position' => $position]);
    }
}
