<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Interfaces\Modules\Core as CoreModuleInterface;
use Flat3\RevPi\Led\CoreLed;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;

class Core extends Module implements CoreModuleInterface
{
    public function getLed(LedPosition $position): Led
    {
        return app(CoreLed::class, ['position' => $position]);
    }
}
