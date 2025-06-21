<?php

declare(strict_types=1);

namespace Flat3\RevPi\JsonRpc;

use Illuminate\Support\Str;

class Request
{
    public string $id;

    public string $method;

    /**
     * @var array<string, int|string|null>
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
     * @param  array<string, string|array<string,int|string>|int|bool>  $data
     */
    public function __unserialize(array $data): void
    {
        assert(is_string($data['id']));
        assert(is_string($data['method']));
        assert(is_array($data['params']));

        $this->id = $data['id'];
        $this->method = $data['method'];
        $this->params = $data['params'];
    }
}
