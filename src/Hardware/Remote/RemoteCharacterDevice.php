<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Interfaces\Hardware\Stream;
use Flat3\RevPi\Rpc\RpcDevice;
use Revolt\EventLoop;

class RemoteCharacterDevice extends RemoteDevice implements Stream
{
    /** @var resource */
    protected mixed $local;

    /** @var resource */
    protected mixed $remote;

    public function __construct(RpcDevice $peer)
    {
        parent::__construct($peer);

        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        assert($sockets !== false);
        [$this->local, $this->remote] = $sockets;
        stream_set_blocking($this->local, false);
        stream_set_blocking($this->remote, false);
    }

    public function close(): int
    {
        fclose($this->remote);
        fclose($this->local);

        return parent::close();
    }

    public function fdopen(): mixed
    {
        $this->device->request('fdopen')->await();

        $this->device->on('data', function (string $payload) {
            fwrite($this->remote, $payload);
        });

        EventLoop::onReadable($this->remote, function ($callbackId, $stream) {
            while (true) {
                $data = @fread($stream, Constants::BlockSize);

                if ($data === false || $data === '') {
                    break;
                }

                $this->write($data, strlen($data));

                if (strlen($data) < Constants::BlockSize) {
                    break;
                }
            }

            if (! is_resource($stream) || @feof($stream)) {
                EventLoop::cancel($callbackId);
            }
        });

        return $this->local;
    }
}
