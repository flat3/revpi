<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Virtual;

use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Seek;

abstract class VirtualBlockDevice implements Device, Seek
{
    protected string $memory = '';

    protected int $pos = 0;

    public function open(string $pathname, int $flags): int
    {
        $this->pos = 0;

        return 0;
    }

    public function close(): int
    {
        return 0;
    }

    public function read(string &$buffer, int $count): int
    {
        $offset = $this->pos;
        $memorySize = strlen($this->memory);

        if ($offset >= $memorySize) {
            $buffer = '';

            return 0;
        }

        $toRead = min($count, $memorySize - $offset);
        $buffer = substr($this->memory, $offset, $toRead);
        $this->pos += $toRead;

        return strlen($buffer);
    }

    public function write(string $buffer, int $count): int
    {
        $offset = $this->pos;
        $memorySize = strlen($this->memory);

        $remaining = $memorySize - $offset;

        if ($remaining <= 0) {
            return -1;
        }

        $writeSize = min($count, $remaining);
        $actualWrite = min(strlen($buffer), $writeSize);

        if (($offset + $actualWrite) > $memorySize) {
            return -1;
        }

        $before = substr($this->memory, 0, $offset);
        $toWrite = substr($buffer, 0, $actualWrite);
        $after = substr($this->memory, $offset + $actualWrite);

        $this->memory = $before.$toWrite.$after;
        $this->pos += $actualWrite;

        return $actualWrite;
    }

    public function lseek(int $offset, int $whence): int
    {
        $memLen = strlen($this->memory);
        $curPos = $this->pos;

        $newPos = match ($whence) {
            SEEK_SET => $offset,
            SEEK_CUR => $curPos + $offset,
            SEEK_END => $memLen + $offset,
            default => throw new NotImplementedException,
        };

        if ($newPos < 0 || $newPos > $memLen) {
            return -1;
        }

        $this->pos = $newPos;

        return $newPos;
    }
}
