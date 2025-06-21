<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\WebsocketClient;
use Flat3\RevPi\Interfaces\Hardware\PiControl;

class RemotePiControlHandler implements WebsocketClientHandler
{
    public function __construct(protected PiControl $piControl)
    {
    }

    public function handleClient(WebsocketClient $client, Request $request, Response $response): void
    {
        while ($message = $client->receive()) {
            $request = unserialize($message->read());

            assert($request instanceof \Flat3\RevPi\JsonRpc\Request);

            $response = new \Flat3\RevPi\JsonRpc\Response;
            $response->id = $request->id;

            $method = $request->method;
            $params = $request->params;

            switch ($method) {
                case 'lseek':
                    assert(is_int($params['offset']) && is_int($params['whence']));
                    $response->result = $this->piControl->lseek($params['offset'], $params['whence']);
                    break;

                case 'ioctl':
                    assert(is_string($params['argp']) && is_int($params['request']));
                    $argp = $params['argp'];
                    $ret = $this->piControl->ioctl($params['request'], $argp);
                    $response->result = [
                        'argp' => $argp,
                        'return' => $ret,
                    ];
                    break;

                case 'read':
                    $buffer = $params['buffer'];
                    $ret = $this->piControl->read($buffer, $params['count']);
                    $response->result = [
                        'buffer' => $buffer,
                        'return' => $ret,
                    ];
                    break;

                case 'write':
                    $response->result = $this->piControl->write($params['buffer'], $params['count']);
                    break;
            }

            $client->sendBinary(serialize($response));
        }
    }
}
