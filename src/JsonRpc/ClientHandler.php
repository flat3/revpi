<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\WebsocketClient;
use Flat3\RevPi\Exceptions\NotSupportedException;
use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\Interfaces\Hardware\Terminal;

class ClientHandler implements WebsocketClientHandler
{
    public function handleClient(WebsocketClient $client, Request $request, Response $response): void
    {
        $target = match ($request->getQueryParameter('device')) {
            'picontrol' => app(PiControl::class),
            'terminal' => app(Terminal::class),
            default => throw new NotSupportedException,
        };

        $handler = app(JsonRpcDevice::class);
        $handler->setDevice($target);

        $handler->attachSocket($client);
        $handler->loop();
    }
}
