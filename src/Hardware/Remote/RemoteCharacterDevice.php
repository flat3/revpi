<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Interfaces\Hardware\Stream;

abstract class RemoteCharacterDevice extends RemoteDevice implements Stream
{
    public function fdopen(): mixed
    {
        throw new NotImplementedException;
    }
}
