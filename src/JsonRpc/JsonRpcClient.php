<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Amp\DeferredFuture;
use Amp\Future;
use Amp\Websocket\Client\WebsocketConnection;
use Flat3\RevPi\Exceptions\RemoteDeviceException;
use Throwable;

use function Amp\async;

class JsonRpcClient
{
    protected WebsocketConnection $socket;

    /**
     * @var array<string, DeferredFuture<mixed>>
     */
    protected array $pending = [];

    public static function connect(WebsocketConnection $connection): self
    {
        $client = new self($connection);
        async(fn () => $client->receiveLoop());

        return $client;
    }

    private function __construct(WebsocketConnection $socket)
    {
        $this->socket = $socket;
    }

    /**
     * @param  array<string, int|string|null>  $params
     * @return Future<int|string|array<string, int|string|null>>
     */
    public function request(string $method, array $params = []): Future
    {
        /** @var DeferredFuture<int|string|array<string, int|string|null>> $deferred */
        $deferred = new DeferredFuture;
        $request = new Request;
        $request->method = $method;
        $request->params = $params;
        $this->pending[$request->id] = $deferred;

        try {
            $this->socket->sendBinary(serialize($request));
        } catch (Throwable $e) {
            $deferred->error($e);
            unset($this->pending[$request->id]);
        }

        return $deferred->getFuture();
    }

    private function receiveLoop(): void
    {
        try {
            while ($message = $this->socket->receive()) {
                $payload = $message->buffer();
                $response = unserialize($payload);

                if (! $response instanceof Response) {
                    continue;
                }

                $id = $response->id;

                if (! isset($this->pending[$id])) {
                    continue;
                }

                $deferred = $this->pending[$id];
                unset($this->pending[$id]);

                if ($response->errorCode !== null) {
                    assert(is_string($response->errorMessage));
                    $deferred->error(new RemoteDeviceException($response->errorMessage, $response->errorCode));

                    return;
                }

                $deferred->complete($response->result);
            }
        } catch (Throwable $t) {
            foreach ($this->pending as $deferred) {
                $deferred->error($t);
            }

            $this->pending = [];
        }
    }

    public function close(): void
    {
        $this->socket->close();
    }
}
