<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware;

use Flat3\RevPi\Contracts\Compact as CompactContract;
use Flat3\RevPi\Hardware\Led\CompactLed;
use Flat3\RevPi\Hardware\Led\Led;
use Flat3\RevPi\Hardware\Led\LedPosition;

class Compact extends BaseModule implements CompactContract
{
    public function led(LedPosition $position): Led
    {
        return app(CompactLed::class, ['position' => $position]);
    }
}
