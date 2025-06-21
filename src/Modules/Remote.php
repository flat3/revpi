<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Amp\Websocket\Client\WebsocketHandshake;
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Hardware\Remote\RemotePiControlDevice;
use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\Interfaces\Led;
use Flat3\RevPi\Interfaces\Modules\Remote as RemoteInterface;
use Flat3\RevPi\Interfaces\ProcessImage as ProcessImageInterface;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Led\RemoteLed;
use Flat3\RevPi\Monitors\Trigger;
use Flat3\RevPi\ProcessImage\ProcessImage;
use Psr\Http\Message\UriInterface as PsrUri;

use function Amp\Websocket\Client\connect;

class Remote implements RemoteInterface
{
    protected PiControl $piControl;

    public function getLed(LedPosition $position): Led
    {
        return app(RemoteLed::class, ['position' => $position, 'image' => $this->getProcessImage()]);
    }

    public function getProcessImage(): ProcessImageInterface
    {
        return app(ProcessImage::class, ['device' => $this->piControl]);
    }

    public function resume(): void
    {
        throw new NotImplementedException;
    }

    public function monitor(Trigger $monitor): void
    {
        throw new NotImplementedException;
    }

    public function connect(WebsocketHandshake|PsrUri|string $handshake): void
    {
        if (! $handshake instanceof WebsocketHandshake) {
            $handshake = new WebsocketHandshake($handshake);
        }

        $this->piControl = app(RemotePiControlDevice::class);
        $this->piControl->socket(connect($handshake));
    }
}
