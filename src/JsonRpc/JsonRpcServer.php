<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\WebsocketClient;
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Ioctl;
use Flat3\RevPi\Interfaces\Hardware\Seek;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\JsonRpc\Request as JsonRpcRequest;
use Flat3\RevPi\JsonRpc\Response as JsonRpcResponse;

abstract class JsonRpcServer implements WebsocketClientHandler
{
    protected Device $device;

    /**
     * @param  array<string, int|string|null>  $params
     */
    public function handle(string $method, array $params): mixed
    {
        switch ($method) {
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
        }

        throw new NotImplementedException;
    }

    public function handleClient(WebsocketClient $client, Request $request, Response $response): void
    {
        while ($message = $client->receive()) {
            $request = $message->read();
            assert(is_string($request));
            $request = unserialize($request);

            assert($request instanceof JsonRpcRequest);

            $response = new JsonRpcResponse;
            $response->id = $request->id;

            $method = $request->method;
            $params = $request->params;

            $response->result = $this->handle($method, $params);

            $client->sendBinary(serialize($response));
        }
    }
}
