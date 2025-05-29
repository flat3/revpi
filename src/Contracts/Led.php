<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\Led\LedColour;

interface Led
{
    public function set(LedColour $colour): void;

    public function get(): LedColour;

    public function off(): void;
}
