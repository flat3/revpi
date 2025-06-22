<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Flat3\RevPi\Interfaces\Hardware\Stream;
use Flat3\RevPi\JsonRpc\Event;
use Flat3\RevPi\JsonRpc\Peer;
use Revolt\EventLoop;

abstract class RemoteCharacterDevice extends RemoteDevice implements Stream
{
    /** @var resource */
    protected mixed $local;

    /** @var resource */
    protected mixed $remote;

    public function __construct(Peer $peer)
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

        $this->peer->on(function (Event $event) {
            if ($event->type === 'readable') {
                fwrite($this->remote, $event->payload);
            }
        });

        EventLoop::onReadable($this->remote, function ($callbackId, $stream) {
            $data = @fread($stream, 8192);
            assert(is_string($data));
            $this->write($data, strlen($data));
        });

        return $this->local;
    }
}
