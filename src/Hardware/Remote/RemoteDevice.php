<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Amp\Websocket\WebsocketClient;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Ioctl;
use Flat3\RevPi\Rpc\RpcDevice;

use function Amp\async;

abstract class RemoteDevice implements Device, Ioctl
{
    public function __construct(protected RpcDevice $device) {}

    public function socket(WebsocketClient $websocket): void
    {
        $this->device->attachSocket($websocket);
        async(fn () => $this->device->loop());
    }

    public function open(string $pathname, int $flags): int
    {
        return (int) $this->device->request('open', ['pathname' => $pathname, 'flags' => $flags])->await();
    }

    public function close(): int
    {
        return (int) $this->device->request('close')->await();
    }

    public function read(string &$buffer, int $count): int
    {
        /** @var array{buffer: string, return: int} $response */
        $response = $this->device->request('read', ['buffer' => $buffer, 'count' => $count])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function write(string $buffer, int $count): int
    {
        return (int) $this->device->request('write', ['buffer' => $buffer, 'count' => $count])->await();
    }

    public function ioctl(int $request, ?string &$argp = null): int
    {
        /** @var array{argp: ?string, return: int} $response */
        $response = $this->device->request('ioctl', ['request' => $request, 'argp' => $argp])->await();

        $argp = $response['argp'];

        return $response['return'];
    }
}
