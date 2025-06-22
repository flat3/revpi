<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Flat3\RevPi\Interfaces\Hardware\Seek;

abstract class RemoteBlockDevice extends RemoteDevice implements Seek
{
    public function lseek(int $offset, int $whence): int
    {
        return (int) $this->device->request('lseek', ['offset' => $offset, 'whence' => $whence])->await();
    }
}
