<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Amp\DeferredFuture;
use Amp\Future;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\WebsocketClient;
use Closure;
use Flat3\RevPi\Exceptions\RemoteDeviceException;
use Flat3\RevPi\JsonRpc\Request as JsonRpcRequest;
use Flat3\RevPi\JsonRpc\Response as JsonRpcResponse;
use Throwable;

use function Amp\async;

/**
 * @phpstan-type JsonRpcDeviceMethodT 'open'|'close'|'lseek'|'ioctl'|'read'|'write'|'cfgetispeed'|'cfgetospeed'|'cfsetispeed'|'cfsetospeed'|'tcflush'|'tcdrain'|'tcsendbreak'|'fdopen'
 * @phpstan-type JsonRpcMethodT JsonRpcDeviceMethodT
 * @phpstan-type JsonRpcDeviceEventTypeT 'readable'
 * @phpstan-type JsonRpcEventTypeT JsonRpcDeviceEventTypeT
 * @phpstan-type JsonRpcRequestParamsT array<string, int|string|null>
 * @phpstan-type JsonRpcRequestT array{id: string, method: JsonRpcMethodT, params: JsonRpcRequestParamsT }
 * @phpstan-type JsonRpcResponseResultT int|string|array<string, int|string|null>
 * @phpstan-type JsonRpcResponseT array{id: string, error: ?array{ code: ?int, message: ?string }, result: JsonRpcResponseResultT }
 * @phpstan-type JsonRpcEventT array{type: JsonRpcEventTypeT, payload: string}
 */
abstract class JsonRpcPeer implements WebsocketClientHandler
{
    protected WebsocketClient $socket;

    /**
     * @var array{readable: array<callable>}
     */
    protected array $callbacks = [
        'readable' => [],
    ];

    /**
     * @var array<DeferredFuture<JsonRpcResponseResultT>>
     */
    protected array $pending = [];

    /**
     * @param  JsonRpcDeviceEventTypeT  $event
     */
    public function on(string $event, Closure $callback): void
    {
        $this->callbacks[$event][] = $callback;
    }

    public function withSocket(WebsocketClient $socket): self
    {
        $this->socket = $socket;
        async(fn () => $this->handleResponse());

        return $this;
    }

    /**
     * @param  JsonRpcMethodT  $method
     * @param  JsonRpcRequestParamsT  $params
     * @return Future<JsonRpcResponseResultT>
     */
    public function request(string $method, array $params = []): Future
    {
        /** @var DeferredFuture<JsonRpcResponseResultT> $deferred */
        $deferred = new DeferredFuture;
        $request = new JsonRpcRequest;
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

    protected function handleResponse(): void
    {
        try {
            while ($message = $this->socket->receive()) {
                $payload = $message->buffer();
                $response = unserialize($payload);

                if ($response instanceof Event) {
                    collect($this->callbacks[$response->type])->each(fn ($callback) => $callback($response->payload));

                    continue;
                }

                if (! $response instanceof JsonRpcResponse) {
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

    public function handleClient(WebsocketClient $client, Request $request, Response $response): void
    {
        $this->socket = $client;

        while ($message = $client->receive()) {
            $request = $message->read();
            assert(is_string($request));
            $request = unserialize($request);

            assert($request instanceof JsonRpcRequest);

            $response = new JsonRpcResponse;
            $response->id = $request->id;

            $method = $request->method;
            $params = $request->params;

            try {
                $response->result = $this->invoke($method, $params);
            } catch (Throwable $t) {
                $response->errorCode = $t->getCode();
                $response->errorMessage = $t->getMessage();
            }

            $client->sendBinary(serialize($response));
        }
    }

    /**
     * @param  JsonRpcMethodT  $method
     * @param  array<string, int|string|null>  $params
     * @return JsonRpcResponseResultT
     */
    abstract protected function invoke(string $method, array $params): mixed;
}
