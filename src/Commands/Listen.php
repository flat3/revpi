<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Router;
use Amp\Http\Server\SocketHttpServer;
use Amp\Socket\InternetAddress;
use Amp\Websocket\Server\Rfc6455Acceptor;
use Amp\Websocket\Server\Websocket;
use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\JsonRpc\JsonRpcServer;
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
        $address = $this->option('address');
        assert(is_string($address));

        /** @var int<0, 65535> $port */
        $port = (int) $this->option('port');
        $server->expose(new InternetAddress($address, $port));

        $router = new Router($server, $logger, new DefaultErrorHandler);

        foreach ([
            'picontrol' => PiControl::class,
            'terminal' => Terminal::class,
        ] as $route => $class) {
            $router->addRoute(
                method: 'GET',
                uri: '/'.$route,
                requestHandler: new Websocket(
                    httpServer: $server,
                    logger: $logger,
                    acceptor: new Rfc6455Acceptor,
                    clientHandler: app(JsonRpcServer::class, [
                        'device' => app($class),
                    ])
                )
            );
        }

        $server->start(
            $router,
            new DefaultErrorHandler
        );

        EventLoop::run();
    }
}
