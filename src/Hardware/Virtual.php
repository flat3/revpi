<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware;

use Flat3\RevPi\Contracts\BaseModule as BaseModuleContract;
use Flat3\RevPi\Hardware\Led\Led;
use Flat3\RevPi\Hardware\Led\LedPosition;
use Flat3\RevPi\Hardware\Led\VirtualLed;

class Virtual extends BaseModule implements BaseModuleContract
{
    public function led(LedPosition $position): Led
    {
        return app(VirtualLed::class, ['position' => $position]);
    }
}
