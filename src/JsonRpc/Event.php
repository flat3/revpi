<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

/**
 * @phpstan-import-type JsonRpcEventT from JsonRpcHandler
 * @phpstan-import-type JsonRpcEventTypeT from JsonRpcHandler
 */
class Event
{
    /**
     * @var JsonRpcEventTypeT
     */
    public string $type;

    public string $payload;

    public function __serialize(): array
    {
        return [
            'jsonrpc' => '2.0',
            'type' => $this->type,
            'payload' => $this->payload,
        ];
    }

    /**
     * @param  JsonRpcEventT  $data
     */
    public function __unserialize(array $data): void
    {
        $this->type = $data['type'];
        $this->payload = $data['payload'];
    }
}
