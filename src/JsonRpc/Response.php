<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

class Response
{
    public string $id;

    public ?int $errorCode = null;

    public ?string $errorMessage = null;

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
     * @param  array<string, mixed>  $data
     */
    public function __unserialize(array $data): void
    {
        assert(is_string($data['id']));

        $this->id = $data['id'];

        if (isset($data['error'])) {
            assert(is_int($data['error']['code'])); // @phpstan-ignore offsetAccess.nonOffsetAccessible
            assert(is_string($data['error']['message']));  // @phpstan-ignore offsetAccess.nonOffsetAccessible

            $this->errorCode = $data['error']['code'];
            $this->errorMessage = $data['error']['message'];

            return;
        }

        $this->result = $data['result'];
    }
}
