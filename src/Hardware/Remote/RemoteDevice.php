<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Amp\Websocket\WebsocketClient;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Ioctl;
use Flat3\RevPi\JsonRpc\Peer;

abstract class RemoteDevice implements Device, Ioctl
{
    protected Peer $socket;

    public function socket(WebsocketClient $websocket): void
    {
        $this->socket = Peer::initiate($websocket);
    }

    public function open(string $pathname, int $flags): int
    {
        return (int) $this->socket->request('open', ['pathname' => $pathname, 'flags' => $flags])->await();
    }

    public function close(): int
    {
        return (int) $this->socket->request('close')->await();
    }

    public function read(string &$buffer, int $count): int
    {
        /** @var array{buffer: string, return: int} $response */
        $response = $this->socket->request('read', ['buffer' => $buffer, 'count' => $count])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function write(string $buffer, int $count): int
    {
        return (int) $this->socket->request('write', ['buffer' => $buffer, 'count' => $count])->await();
    }

    public function ioctl(int $request, ?string &$argp = null): int
    {
        /** @var array{argp: ?string, return: int} $response */
        $response = $this->socket->request('ioctl', ['request' => $request, 'argp' => $argp])->await();

        $argp = $response['argp'];

        return $response['return'];
    }
}
