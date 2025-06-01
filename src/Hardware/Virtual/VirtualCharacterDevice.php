<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Virtual;

use Flat3\RevPi\Exceptions\PosixDeviceException;
use Flat3\RevPi\Hardware\Interfaces\DeviceInterface;
use Flat3\RevPi\Hardware\Interfaces\IoctlInterface;
use Flat3\RevPi\Hardware\Interfaces\StreamInterface;

abstract class VirtualCharacterDevice implements DeviceInterface, IoctlInterface, StreamInterface
{
    /** @var resource */
    protected mixed $local;

    /** @var resource */
    protected mixed $remote;

    public function open(string $pathname, int $flags): int
    {
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        assert($sockets !== false);
        [$this->local, $this->remote] = $sockets;

        return 0;
    }

    public function close(): int
    {
        fclose($this->remote);
        fclose($this->local);

        return 0;
    }

    public function read(string &$buffer, int $count): int
    {
        if ($count === 0) {
            $buffer = '';

            return 0;
        }

        assert($count > 0);
        $result = fread($this->local, $count);

        if ($result === false) {
            throw new PosixDeviceException;
        }

        $buffer = $result;

        return strlen($buffer);
    }

    public function write(string $buffer, int $count): int
    {
        assert($count > 0);

        $result = fwrite($this->local, $buffer, $count);

        if ($result === false) {
            throw new PosixDeviceException;
        }

        return $result;
    }

    /**
     * @return resource
     */
    public function fdopen(): mixed
    {
        return $this->local;
    }

    /** @return resource */
    public function getRemoteSocket(): mixed
    {
        return $this->remote;
    }
}
