<?php

declare(strict_types=1);

namespace Flat3\RevPi\Rpc;

use Amp\Websocket\WebsocketClosedException;
use Flat3\RevPi\Constants;
use Flat3\RevPi\Exceptions\NotSupportedException;
use Flat3\RevPi\Interfaces\Hardware\Device;
use Flat3\RevPi\Interfaces\Hardware\Ioctl;
use Flat3\RevPi\Interfaces\Hardware\Seek;
use Flat3\RevPi\Interfaces\Hardware\Stream;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Revolt\EventLoop;

/**
 * @phpstan-import-type RpcDeviceMethodT from RpcHandler
 * @phpstan-import-type RpcResponseResultT from RpcHandler
 */
class RpcDevice extends RpcHandler
{
    protected Device $device;

    public function setDevice(Device $device): void
    {
        $this->device = $device;
    }

    /**
     * @param  RpcDeviceMethodT  $method
     * @param  array<string, int|string|null>  $params
     * @return RpcResponseResultT
     */
    protected function invoke(string $method, array $params): mixed
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
                    while (true) {
                        $data = @fread($stream, Constants::BlockSize);

                        if (! is_string($data) || $data === '') {
                            break;
                        }

                        $event = new Event;
                        $event->type = 'data';
                        $event->payload = $data;

                        try {
                            $this->socket->sendBinary(serialize($event));
                        } catch (WebsocketClosedException) {
                            EventLoop::cancel($callbackId);
                            break;
                        }

                        if (strlen($data) < Constants::BlockSize) {
                            break;
                        }
                    }

                    if (! is_resource($stream) || @feof($stream)) {
                        EventLoop::cancel($callbackId);
                    }
                });

                return 0;
        }

        throw new NotSupportedException; // @phpstan-ignore deadCode.unreachable
    }
}
