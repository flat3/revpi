<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Monitors\Trigger;

interface Module
{
    public function led(LedPosition $position): Led;

    public function image(): ProcessImage;

    public function resume(): void;

    public function monitor(Trigger $monitor): void;
}
