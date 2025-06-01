<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\PosixDevice;

use Flat3\RevPi\Exceptions\NotImplementedException;

abstract class VirtualPosixDevice implements PosixDevice
{
    protected string $memory;

    /**
     * @var array<int, int>
     */
    protected array $handles = [];

    public function open(string $pathname, int $flags): int
    {
        $fd = count($this->handles) + 1;
        $this->handles[$fd] = 0;

        return $fd;
    }

    public function close(int $fd): int
    {
        if (!array_key_exists($fd, $this->handles)) {
            return -1;
        }

        unset($this->handles[$fd]);

        return 0;
    }

    public function read(int $fd, string &$buffer, int $count): int
    {
        if (!array_key_exists($fd, $this->handles)) {
            return -1;
        }

        $offset = $this->handles[$fd];
        $memorySize = strlen($this->memory);

        if ($offset >= $memorySize) {
            $buffer = '';

            return 0;
        }

        $toRead = min($count, $memorySize - $offset);
        $buffer = substr($this->memory, $offset, $toRead);
        $this->handles[$fd] += $toRead;

        return strlen($buffer);
    }

    public function write(int $fd, string $buffer, int $count): int
    {
        if (!array_key_exists($fd, $this->handles)) {
            return -1;
        }

        $offset = $this->handles[$fd];
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
        $this->handles[$fd] += $actualWrite;

        return $actualWrite;
    }

    public function lseek(int $fd, int $offset, int $whence): int
    {
        if (!array_key_exists($fd, $this->handles)) {
            return -1;
        }

        $memLen = strlen($this->memory);
        $curPos = $this->handles[$fd];

        $newPos = match ($whence) {
            SEEK_SET => $offset,
            SEEK_CUR => $curPos + $offset,
            SEEK_END => $memLen + $offset,
            default => throw new NotImplementedException,
        };

        if ($newPos < 0 || $newPos > $memLen) {
            return -1;
        }

        $this->handles[$fd] = $newPos;

        return $newPos;
    }

    public function stream(int $fd): mixed
    {
        $stream = fopen('php://memory', 'r+');

        fwrite($stream, $this->memory);
        rewind($stream);

        return $stream;
    }
}
