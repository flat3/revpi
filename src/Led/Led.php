<?php

declare(strict_types=1);

namespace Flat3\RevPi\Led;

use Flat3\RevPi\Interfaces\Led as LedInterface;
use Flat3\RevPi\Interfaces\ProcessImage;

abstract class Led implements LedInterface
{
    public function __construct(protected LedPosition $position, protected ProcessImage $image)
    {
        $this->position($this->position);
    }

    public function set(LedColour $colour): void
    {
        /** @var int $shift */
        $shift = $this->position($this->position);

        /** @var int $value */
        $value = $this->value($colour);

        $current = $this->image->readVariable('RevPiLED');
        $mask = ((1 << 3) - 1) << $shift;
        $next = ($current & ~$mask) | ($value << $shift);
        $this->image->writeVariable('RevPiLED', $next);
    }

    public function get(): LedColour
    {
        /** @var int $shift */
        $shift = $this->position($this->position);
        $current = $this->image->readVariable('RevPiLED');

        $mask = ((1 << 3) - 1) << $shift;
        $value = ($current & $mask) >> $shift;

        /** @var LedColour */
        return $this->value($value);
    }

    public function off(): void
    {
        $this->set(LedColour::Off);
    }

    abstract protected function position(LedPosition|int $position): int|LedPosition;

    abstract protected function value(LedColour|int $colour): int|LedColour;
}
