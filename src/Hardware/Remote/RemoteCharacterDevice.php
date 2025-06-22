<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Interfaces\Hardware\Stream;
use Flat3\RevPi\JsonRpc\JsonRpcDevice;
use Revolt\EventLoop;

abstract class RemoteCharacterDevice extends RemoteDevice implements Stream
{
    /** @var resource */
    protected mixed $local;

    /** @var resource */
    protected mixed $remote;

    public function __construct(JsonRpcDevice $peer)
    {
        parent::__construct($peer);

        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        assert($sockets !== false);
        [$this->local, $this->remote] = $sockets;
        stream_set_blocking($this->local, false);
        stream_set_blocking($this->remote, false);
    }

    public function fdopen(): mixed
    {
        $this->peer->request('fdopen')->await();

        $this->peer->on('readable', function (string $payload) {
            fwrite($this->remote, $payload);
        });

        EventLoop::onReadable($this->remote, function ($callbackId, $stream) {
            $data = @fread($stream, Constants::BlockSize);

            if (is_string($data) && $data !== '') {
                $this->write($data, strlen($data));
            } elseif (! is_resource($stream) || @feof($stream)) {
                EventLoop::cancel($callbackId);
            }
        });

        return $this->local;
    }
}
