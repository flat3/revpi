<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Amp\Websocket\WebsocketClient;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Ioctl;
use Flat3\RevPi\JsonRpc\Peer;

abstract class RemoteDevice implements Device, Ioctl
{
    public function __construct(protected Peer $peer) {}

    public function socket(WebsocketClient $websocket): void
    {
        $this->peer->setSocket($websocket);
    }

    public function open(string $pathname, int $flags): int
    {
        return (int) $this->peer->request('open', ['pathname' => $pathname, 'flags' => $flags])->await();
    }

    public function close(): int
    {
        return (int) $this->peer->request('close')->await();
    }

    public function read(string &$buffer, int $count): int
    {
        /** @var array{buffer: string, return: int} $response */
        $response = $this->peer->request('read', ['buffer' => $buffer, 'count' => $count])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function write(string $buffer, int $count): int
    {
        return (int) $this->peer->request('write', ['buffer' => $buffer, 'count' => $count])->await();
    }

    public function ioctl(int $request, ?string &$argp = null): int
    {
        /** @var array{argp: ?string, return: int} $response */
        $response = $this->peer->request('ioctl', ['request' => $request, 'argp' => $argp])->await();

        $argp = $response['argp'];

        return $response['return'];
    }
}
