<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Amp\Websocket\Client\WebsocketConnection;
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Seek;
use Flat3\RevPi\JsonRpc\JsonRpcWebsocketClient;

abstract class RemoteBlockDevice implements Device, Seek
{
    protected JsonRpcWebsocketClient $client;

    protected WebsocketConnection $websocket;

    public function socket(WebsocketConnection $websocket): void
    {
        $this->websocket = $websocket;
        $this->client = JsonRpcWebsocketClient::connect($websocket);
    }

    public function open(string $pathname, int $flags): int
    {
        return $this->client->request('open', ['pathname' => $pathname, 'flags' => $flags])->await();
    }

    public function close(): int
    {
        throw new NotImplementedException;
    }

    public function read(string &$buffer, int $count): int
    {
        $response = $this->client->request('read', ['buffer' => $buffer, 'count' => $count])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function write(string $buffer, int $count): int
    {
        return $this->client->request('write', ['buffer' => $buffer, 'count' => $count])->await();
    }

    public function lseek(int $offset, int $whence): int
    {
        return $this->client->request('lseek', ['offset' => $offset, 'whence' => $whence])->await();
    }
}
