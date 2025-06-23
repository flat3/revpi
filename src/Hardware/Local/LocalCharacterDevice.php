<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Local;

use Flat3\RevPi\Interfaces\Hardware\Stream;

class LocalCharacterDevice extends LocalDevice implements Stream
{
    public function fdopen(): mixed
    {
        $stream = fopen("php://fd/{$this->fd}", 'r+b');
        assert($stream !== false);
        stream_set_blocking($stream, false);

        return $stream;
    }
}
