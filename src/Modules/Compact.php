<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Interfaces\Modules\Compact as CompactModuleInterface;
use Flat3\RevPi\Led\CompactLed;
use Flat3\RevPi\Led\Led;
use Flat3\RevPi\Led\LedPosition;

class Compact extends Module implements CompactModuleInterface
{
    public function led(LedPosition $position): Led
    {
        return app(CompactLed::class, ['position' => $position]);
    }
}
