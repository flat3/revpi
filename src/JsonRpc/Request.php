<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Illuminate\Support\Str;

/**
 * @phpstan-import-type JsonRpcMethodT from JsonRpcHandler
 * @phpstan-import-type JsonRpcRequestT from JsonRpcHandler
 * @phpstan-import-type JsonRpcRequestParamsT from JsonRpcHandler
 */
class Request
{
    public string $id;

    /** @var JsonRpcMethodT */
    public string $method;

    /**
     * @var JsonRpcRequestParamsT
     */
    public array $params = [];

    public function __construct()
    {
        $this->id = (string) Str::uuid();
    }

    public function __serialize(): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $this->id,
            'method' => $this->method,
            'params' => $this->params,
        ];
    }

    /**
     * @param  JsonRpcRequestT  $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->method = $data['method'];
        $this->params = $data['params'];
    }
}
