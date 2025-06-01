<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Led\LedColour;

interface Led
{
    public function set(LedColour $colour): void;

    public function get(): LedColour;

    public function off(): void;
}
