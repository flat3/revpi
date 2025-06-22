<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Amp\Websocket\Client\WebsocketHandshake;
use Flat3\RevPi\Hardware\Remote\RemotePiControlDevice;
use Flat3\RevPi\Hardware\Remote\RemoteTerminalDevice;
use Flat3\RevPi\Interfaces\Led;
use Flat3\RevPi\Interfaces\Modules\Remote as RemoteInterface;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Interfaces\SerialPort;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Led\RemoteLed;
use Psr\Http\Message\UriInterface as PsrUri;

use function Amp\Websocket\Client\connect;

class Remote extends Module implements RemoteInterface
{
    protected WebsocketHandshake $handshake;

    public function getLed(LedPosition $position): Led
    {
        return app(RemoteLed::class, ['position' => $position, 'image' => $this->getProcessImage()]);
    }

    public function getProcessImage(): ProcessImage
    {
        if ($this->processImage instanceof ProcessImage) {
            return $this->processImage;
        }

        $piControl = app(RemotePiControlDevice::class);
        $piControl->socket(connect($this->handshake->withQueryParameter('device', 'picontrol')));

        $this->processImage = app(ProcessImage::class, ['device' => $piControl]);

        return $this->processImage;
    }

    public function getSerialPort(string $devicePath): SerialPort
    {
        $terminal = app(RemoteTerminalDevice::class);
        $terminal->socket(connect($this->handshake->withQueryParameter('device', 'terminal')));

        return app(SerialPort::class, ['devicePath' => $devicePath, 'device' => $terminal]);
    }

    public function handshake(WebsocketHandshake|PsrUri|string $handshake): void
    {
        if (! $handshake instanceof WebsocketHandshake) {
            $handshake = new WebsocketHandshake($handshake);
        }

        $this->handshake = $handshake;
    }
}
