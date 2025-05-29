<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\Led\LedPosition;
use Flat3\RevPi\Monitors\Trigger;

interface BaseModule
{
    public function led(LedPosition $position): Led;

    public function image(): ProcessImage;

    public function resume(): void;

    public function monitor(Trigger $monitor): void;
}
