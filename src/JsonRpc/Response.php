<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

/**
 * @phpstan-import-type JsonRpcResponseT from Peer
 * @phpstan-import-type JsonRpcResponseResultT from Peer
 */
class Response
{
    public string $id;

    public ?int $errorCode = null;

    public ?string $errorMessage = null;

    /**
     * @var JsonRpcResponseResultT
     */
    public mixed $result;

    public function __serialize(): array
    {
        $response = [
            'jsonrpc' => '2.0',
            'id' => $this->id,
        ];

        if ($this->errorCode !== null) {
            $response['error'] = [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
            ];

            return $response;
        }

        $response['result'] = $this->result;

        return $response;
    }

    /**
     * @param  JsonRpcResponseT  $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];

        if (isset($data['error'])) {
            $this->errorCode = $data['error']['code'];
            $this->errorMessage = $data['error']['message'];

            return;
        }

        $this->result = $data['result'];
    }
}
