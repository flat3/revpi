<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\SocketHttpServer;
use Amp\Socket\InternetAddress;
use Amp\Websocket\Server\Rfc6455Acceptor;
use Amp\Websocket\Server\Websocket;
use Flat3\RevPi\JsonRpc\ClientHandler;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Revolt\EventLoop;

class Listen extends Command
{
    protected $description = 'Listen for incoming websocket requests';

    protected $signature = 'revpi:listen {--address=0.0.0.0} {--port=12873}';

    public function handle(LoggerInterface $logger): void
    {
        $address = $this->option('address');
        assert(is_string($address));

        /** @var int<0, 65535> $port */
        $port = (int) $this->option('port');

        $server = SocketHttpServer::createForDirectAccess($logger);
        $server->expose(new InternetAddress($address, $port));

        $socket = new Websocket(
            httpServer: $server,
            logger: $logger,
            acceptor: app(Rfc6455Acceptor::class),
            clientHandler: app(ClientHandler::class),
        );

        $server->start($socket, new DefaultErrorHandler);

        EventLoop::run();
    }
}
