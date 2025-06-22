<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Led\LedColour;

interface Led
{
    /**
     * Set the LED to the specified colour.
     *
     * @param  LedColour  $colour  The colour to set the LED.
     */
    public function set(LedColour $colour): void;

    /**
     * Get the current colour of the LED.
     *
     * @return LedColour The current colour of the LED.
     */
    public function get(): LedColour;

    /**
     * Turn off the LED.
     */
    public function off(): void;
}
