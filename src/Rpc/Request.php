<?php

declare(strict_types=1);

namespace Flat3\RevPi\Rpc;

use Illuminate\Support\Str;

/**
 * @phpstan-import-type RpcMethodT from RpcHandler
 * @phpstan-import-type RpcRequestT from RpcHandler
 * @phpstan-import-type RpcRequestParamsT from RpcHandler
 */
class Request
{
    public string $id;

    /** @var RpcMethodT */
    public string $method;

    /**
     * @var RpcRequestParamsT
     */
    public array $params = [];

    public function __construct()
    {
        $this->id = (string) Str::uuid();
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'method' => $this->method,
            'params' => $this->params,
        ];
    }

    /**
     * @param  RpcRequestT  $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->method = $data['method'];
        $this->params = $data['params'];
    }
}
