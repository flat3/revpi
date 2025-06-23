<?php

declare(strict_types=1);

namespace Flat3\RevPi\Rpc;

/**
 * @phpstan-import-type RpcEventT from RpcHandler
 * @phpstan-import-type RpcEventTypeT from RpcHandler
 */
class Event
{
    /**
     * @var RpcEventTypeT
     */
    public string $type;

    public string $payload;

    public function __serialize(): array
    {
        return [
            'type' => $this->type,
            'payload' => $this->payload,
        ];
    }

    /**
     * @param  RpcEventT  $data
     */
    public function __unserialize(array $data): void
    {
        $this->type = $data['type'];
        $this->payload = $data['payload'];
    }
}
