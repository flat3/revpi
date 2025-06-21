<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Modules;

use Amp\Websocket\Client\WebsocketHandshake;
use Flat3\RevPi\Interfaces\Module;
use Psr\Http\Message\UriInterface as PsrUri;

interface Remote extends Module
{
    public function connect(WebsocketHandshake|PsrUri|string $handshake):void;
}
