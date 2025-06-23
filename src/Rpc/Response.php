<?php

declare(strict_types=1);

namespace Flat3\RevPi\Rpc;

/**
 * @phpstan-import-type RpcResponseT from RpcHandler
 * @phpstan-import-type RpcResponseResultT from RpcHandler
 */
class Response
{
    public string $id;

    public ?int $errorCode = null;

    public ?string $errorMessage = null;

    /**
     * @var RpcResponseResultT
     */
    public mixed $result;

    public function __serialize(): array
    {
        $response = [
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
     * @param  RpcResponseT  $data
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
