<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

interface Stream
{
    /** @return resource */
    public function fdopen(): mixed;
}
