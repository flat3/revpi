<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Amp\DeferredFuture;
use Amp\Future;
use Amp\Websocket\WebsocketClient;
use Closure;
use Flat3\RevPi\Exceptions\RemoteDeviceException;
use Throwable;

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
abstract class JsonRpcHandler
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

    public function attachSocket(WebsocketClient $socket): self
    {
        $this->socket = $socket;

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

    public function loop(): void
    {
        try {
            while ($message = $this->socket->receive()) {
                $payload = $message->buffer();
                $packet = unserialize($payload);

                if ($packet instanceof Event) {
                    collect($this->callbacks[$packet->type])->each(fn ($callback) => $callback($packet->payload));

                    continue;
                }

                if ($packet instanceof Request) {
                    $response = new Response;
                    $response->id = $packet->id;

                    $method = $packet->method;
                    $params = $packet->params;

                    try {
                        $response->result = $this->invoke($method, $params);
                    } catch (Throwable $t) {
                        $response->errorCode = $t->getCode();
                        $response->errorMessage = $t->getMessage();
                    }

                    $this->socket->sendBinary(serialize($response));
                }

                if ($packet instanceof Response) {
                    $id = $packet->id;

                    if (! isset($this->pending[$id])) {
                        continue;
                    }

                    $deferred = $this->pending[$id];
                    unset($this->pending[$id]);

                    if ($packet->errorCode !== null) {
                        assert(is_string($packet->errorMessage));
                        $deferred->error(new RemoteDeviceException($packet->errorMessage, $packet->errorCode));

                        continue;
                    }

                    $deferred->complete($packet->result);
                }
            }
        } catch (Throwable $t) {
            foreach ($this->pending as $deferred) {
                $deferred->error($t);
            }

            $this->pending = [];
        }
    }

    /**
     * @param  JsonRpcMethodT  $method
     * @param  array<string, int|string|null>  $params
     * @return JsonRpcResponseResultT
     */
    abstract protected function invoke(string $method, array $params): mixed;
}
