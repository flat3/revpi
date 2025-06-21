<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Router;
use Amp\Http\Server\SocketHttpServer;
use Amp\Socket\InternetAddress;
use Amp\Websocket\Server\Rfc6455Acceptor;
use Amp\Websocket\Server\Websocket;
use Flat3\RevPi\Hardware\Remote\RemotePiControlHandler;
use Flat3\RevPi\Hardware\Virtual\VirtualPiControlDevice;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Revolt\EventLoop;

class Listen extends Command
{
    protected $description = 'Start the server';

    protected $signature = 'revpi:listen {--address=0.0.0.0} {--port=12873}';

    public function handle(LoggerInterface $logger): void
    {
        $server = SocketHttpServer::createForDirectAccess($logger);
        $server->expose(new InternetAddress((string) $this->option('address'), (int) $this->option('port')));

        $router = new Router($server, $logger, new DefaultErrorHandler);
        $router->addRoute(
            method: 'GET',
            uri: '/picontrol',
            requestHandler: new Websocket($server, $logger, new Rfc6455Acceptor, app(RemotePiControlHandler::class, ['piControl' => app(VirtualPiControlDevice::class)]))
        );

        $server->start(
            $router,
            new DefaultErrorHandler
        );

        EventLoop::run();
    }
}
