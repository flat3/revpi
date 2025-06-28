<?php

declare(strict_types=1);

namespace Flat3\RevPi\Rpc;

use Amp\DeferredFuture;
use Amp\Future;
use Amp\Websocket\WebsocketClient;
use Closure;
use Flat3\RevPi\Exceptions\RemoteDeviceException;
use Throwable;

/**
 * @phpstan-type RpcDeviceMethodT 'open'|'close'|'lseek'|'ioctl'|'read'|'write'|'cfgetispeed'|'cfgetospeed'|'cfsetispeed'|'cfsetospeed'|'tcflush'|'tcdrain'|'tcsendbreak'|'fdopen'
 * @phpstan-type RpcMethodT RpcDeviceMethodT
 * @phpstan-type RpcDeviceEventTypeT 'readable'
 * @phpstan-type RpcEventTypeT RpcDeviceEventTypeT
 * @phpstan-type RpcRequestParamsT array<string, int|string|null>
 * @phpstan-type RpcRequestT array{id: string, method: RpcMethodT, params: RpcRequestParamsT }
 * @phpstan-type RpcResponseResultT int|string|array<string, int|string|null>
 * @phpstan-type RpcResponseT array{id: string, error: ?array{ code: ?int, message: ?string }, result: RpcResponseResultT }
 * @phpstan-type RpcEventT array{type: RpcEventTypeT, payload: string}
 */
abstract class RpcHandler
{
    protected WebsocketClient $socket;

    /**
     * @var array{data: array<callable>}
     */
    protected array $callbacks = [
        'data' => [],
    ];

    /**
     * @var array<DeferredFuture<RpcResponseResultT>>
     */
    protected array $pending = [];

    /**
     * @param  RpcDeviceEventTypeT  $event
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
     * @param  RpcMethodT  $method
     * @param  RpcRequestParamsT  $params
     * @return Future<RpcResponseResultT>
     */
    public function request(string $method, array $params = []): Future
    {
        /** @var DeferredFuture<RpcResponseResultT> $deferred */
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
     * @param  RpcMethodT  $method
     * @param  array<string, int|string|null>  $params
     * @return RpcResponseResultT
     */
    abstract protected function invoke(string $method, array $params): mixed;
}
