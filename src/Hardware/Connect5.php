<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware;

use Flat3\RevPi\Contracts\Connect5 as Connect5Contract;
use Flat3\RevPi\Hardware\Led\Connect5Led;
use Flat3\RevPi\Hardware\Led\Led;
use Flat3\RevPi\Hardware\Led\LedPosition;

class Connect5 extends BaseModule implements Connect5Contract
{
    public function led(LedPosition $position): Led
    {
        return app(Connect5Led::class, ['position' => $position]);
    }
}
