<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Amp\DeferredFuture;
use Amp\Future;
use Amp\Websocket\Client\WebsocketConnection;
use RuntimeException;

use function Amp\async;

class JsonRpcWebsocketClient
{
    protected WebsocketConnection $connection;

    /**
     * @var array<string, DeferredFuture>
     */
    protected array $pending = [];

    public static function connect($connection): self
    {
        $client = new self($connection);
        async(fn () => $client->receiveLoop());

        return $client;
    }

    private function __construct(WebsocketConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Future<int|string|bool|array<string, int|string|array<string, int|string|bool>|bool>>
     */
    public function request(string $method, array $params = []): Future
    {
        $deferred = new DeferredFuture;
        $request = new Request;
        $request->method = $method;
        $request->params = $params;
        $this->pending[$request->id] = $deferred;

        try {
            $this->connection->sendBinary(serialize($request));
        } catch (\Throwable $e) {
            $deferred->error($e);
            unset($this->pending[$request->id]);
        }

        return $deferred->getFuture();
    }

    private function receiveLoop(): void
    {
        try {
            while ($message = $this->connection->receive()) {
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
                    $deferred->error(new RuntimeException($response->errorMessage, $response->errorCode));

                    return;
                }

                $deferred->complete($response->result);
            }
        } catch (\Throwable $t) {
            foreach ($this->pending as $deferred) {
                $deferred->error($t);
            }

            $this->pending = [];
        }
    }

    public function close(): void
    {
        $this->connection->close();
    }
}
