<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Amp\Websocket\Client\WebsocketHandshake;
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Hardware\Remote\RemotePiControlDevice;
use Flat3\RevPi\Hardware\Remote\RemoteTerminalDevice;
use Flat3\RevPi\Interfaces\Led;
use Flat3\RevPi\Interfaces\Modules\Remote as RemoteInterface;
use Flat3\RevPi\Interfaces\ProcessImage as ProcessImageInterface;
use Flat3\RevPi\Interfaces\SerialPort;
use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Led\RemoteLed;
use Flat3\RevPi\Monitors\Trigger;
use Flat3\RevPi\ProcessImage\ProcessImage;

use function Amp\Websocket\Client\connect;

class Remote implements RemoteInterface
{
    protected WebsocketHandshake $handshake;

    public function getLed(LedPosition $position): Led
    {
        return app(RemoteLed::class, ['position' => $position, 'image' => $this->getProcessImage()]);
    }

    public function getProcessImage(): ProcessImageInterface
    {
        $piControl = app(RemotePiControlDevice::class);
        $piControl->socket(connect($this->handshake->withQueryParameter('device', 'picontrol')));

        return app(ProcessImage::class, ['device' => $piControl]);
    }

    public function getSerialPort(string $devicePath): SerialPort
    {
        $terminal = app(RemoteTerminalDevice::class);
        $terminal->socket(connect($this->handshake->withQueryParameter('device', 'terminal')));

        return app(SerialPort::class, ['devicePath' => $devicePath, 'device' => $terminal]);
    }

    public function resume(): void
    {
        throw new NotImplementedException;
    }

    public function monitor(Trigger $monitor): void
    {
        throw new NotImplementedException;
    }

    public function connection(WebsocketHandshake $socket): void
    {
        $this->handshake = $socket;
    }
}
