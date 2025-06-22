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
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Exceptions\RemoteDeviceException;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Ioctl;
use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\Interfaces\Hardware\Seek;
use Flat3\RevPi\Interfaces\Hardware\Stream;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\JsonRpc\Request as JsonRpcRequest;
use Flat3\RevPi\JsonRpc\Response as JsonRpcResponse;
use Revolt\EventLoop;
use Throwable;

use function Amp\async;

/**
 * @phpstan-type JsonRpcMethodT 'open'|'close'|'lseek'|'ioctl'|'read'|'write'|'cfgetispeed'|'cfgetospeed'|'cfsetispeed'|'cfsetospeed'|'tcflush'|'tcdrain'|'tcsendbreak'|'fdopen'
 * @phpstan-type JsonRpcEventTypeT 'readable'
 * @phpstan-type JsonRpcRequestParamsT array<string, int|string|null>
 * @phpstan-type JsonRpcRequestT array{id: string, method: JsonRpcMethodT, params: JsonRpcRequestParamsT }
 * @phpstan-type JsonRpcResponseResultT int|string|array<string, int|string|null>
 * @phpstan-type JsonRpcResponseT array{id: string, error: ?array{ code: ?int, message: ?string }, result: JsonRpcResponseResultT }
 * @phpstan-type JsonRpcEventT array{type: JsonRpcEventTypeT, payload: string}
 */
class JsonRpcPeer implements WebsocketClientHandler
{
    protected WebsocketClient $socket;

    protected Device $device;

    /**
     * @var callable
     */
    protected mixed $eventReceiver;

    /**
     * @var array<DeferredFuture<JsonRpcResponseResultT>>
     */
    protected array $pending = [];

    public function withSocket(WebsocketClient $socket): self
    {
        $this->socket = $socket;
        async(fn () => $this->handleResponse());

        return $this;
    }

    public function withDevice(Device $device): self
    {
        $this->device = $device;

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
                    call_user_func($this->eventReceiver, $response);

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

    public function close(): void
    {
        $this->socket->close();
    }

    public function on(Closure $callback): void
    {
        $this->eventReceiver = $callback;
    }

    /**
     * @param  JsonRpcMethodT  $method
     * @param  array<string, int|string|null>  $params
     * @return JsonRpcResponseResultT
     */
    public function handle(string $method, array $params): mixed
    {
        switch ($method) {
            case 'open':
                /** @var array{pathname: string, flags: int} $params */
                return $this->device->open($params['pathname'], $params['flags']);

            case 'lseek':
                assert($this->device instanceof Seek);

                /** @var array{offset: int, whence: int} $params */
                return $this->device->lseek($params['offset'], $params['whence']);

            case 'ioctl':
                assert($this->device instanceof Ioctl);
                /** @var array{request:int, argp: string} $params */
                $argp = $params['argp'];
                $ret = $this->device->ioctl($params['request'], $argp);

                return [
                    'argp' => $argp,
                    'return' => $ret,
                ];

            case 'read':
                /** @var array{buffer: string, count: int} $params */
                $buffer = $params['buffer'];
                $ret = $this->device->read($buffer, $params['count']);

                return [
                    'buffer' => $buffer,
                    'return' => $ret,
                ];

            case 'write':
                /** @var array{buffer: string, count: int} $params */
                return $this->device->write($params['buffer'], $params['count']);

            case 'close':
                return $this->device->close();

            case 'cfgetispeed':
                assert($this->device instanceof Terminal);
                /** @var array{buffer: string} $params */
                $buffer = $params['buffer'];
                $ret = $this->device->cfgetispeed($buffer);

                return [
                    'buffer' => $buffer,
                    'return' => $ret,
                ];

            case 'cfgetospeed':
                assert($this->device instanceof Terminal);
                /** @var array{buffer: string} $params */
                $buffer = $params['buffer'];
                $ret = $this->device->cfgetospeed($buffer);

                return [
                    'buffer' => $buffer,
                    'return' => $ret,
                ];

            case 'cfsetispeed':
                assert($this->device instanceof Terminal);
                /** @var array{buffer: string, speed: int} $params */
                $buffer = $params['buffer'];
                $ret = $this->device->cfsetispeed($buffer, $params['speed']);

                return [
                    'buffer' => $buffer,
                    'return' => $ret,
                ];

            case 'cfsetospeed':
                assert($this->device instanceof Terminal);
                /** @var array{buffer: string, speed: int} $params */
                $buffer = $params['buffer'];
                $ret = $this->device->cfsetospeed($buffer, $params['speed']);

                return [
                    'buffer' => $buffer,
                    'return' => $ret,
                ];

            case 'tcflush':
                assert($this->device instanceof Terminal);

                /** @var array{queue_selector: int} $params */
                return $this->device->tcflush($params['queue_selector']);

            case 'tcdrain':
                assert($this->device instanceof Terminal);

                return $this->device->tcdrain();

            case 'tcsendbreak':
                assert($this->device instanceof Terminal);

                /** @var array{duration: int} $params */
                return $this->device->tcsendbreak($params['duration']);

            case 'fdopen':
                assert($this->device instanceof Stream);
                $stream = $this->device->fdopen();

                EventLoop::onReadable($stream, function ($callbackId, $stream) {
                    $newData = @fread($stream, 8192);

                    if (is_string($newData) && $newData !== '') {
                        $request = new Event;
                        $request->type = 'readable';
                        $request->payload = $newData;

                        $this->socket->sendBinary(serialize($request));
                    } elseif (! is_resource($stream) || @feof($stream)) {
                        EventLoop::cancel($callbackId);
                    }
                });

                return 0;
        }

        throw new NotImplementedException; // @phpstan-ignore deadCode.unreachable
    }

    public function handleClient(WebsocketClient $client, Request $request, Response $response): void
    {
        $this->device = match ($request->getQueryParameter('device')) {
            'picontrol' => app(PiControl::class),
            'terminal' => app(Terminal::class),
            default => throw new NotImplementedException,
        };

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
                $response->result = $this->handle($method, $params);
            } catch (Throwable $t) {
                $response->errorCode = $t->getCode();
                $response->errorMessage = $t->getMessage();
            }

            $client->sendBinary(serialize($response));
        }
    }
}
