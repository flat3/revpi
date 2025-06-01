<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Monitors\Trigger;

interface Module
{
    public function getLed(LedPosition $position): Led;

    public function getProcessImage(): ProcessImage;

    public function resume(): void;

    public function monitor(Trigger $monitor): void;
}
