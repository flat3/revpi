<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Modules;

use Amp\Websocket\Client\WebsocketHandshake;
use Flat3\RevPi\Interfaces\Module;

interface Remote extends Module
{
    public function handshake(WebsocketHandshake $handshake): void;
}
