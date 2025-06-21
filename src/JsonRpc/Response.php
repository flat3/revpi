<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

class Response
{
    public string $id;

    public ?int $errorCode = null;

    public ?string $errorMessage = null;

    /**
     * @var array<string, int|bool|array|string>|int|string
     */
    public array|int|string $result;

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
     * @param  array<string, string|array|int|bool>  $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];

        if (isset($data['error'])) {
            $this->errorCode = $data['errorCode'];
            $this->errorMessage = $data['errorMessage'];

            return;
        }

        $this->result = $data['result'];
    }
}
