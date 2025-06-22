<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

/**
 * @phpstan-import-type JsonRpcEventT from JsonRpcPeer
 */
class Event
{
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
     * @param  JsonRpcEventT  $data
     */
    public function __unserialize(array $data): void
    {
        $this->type = $data['type'];
        $this->payload = $data['payload'];
    }
}
