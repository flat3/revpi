<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware;

use ArrayAccess;
use Flat3\RevPi\Interfaces\Hardware\Struct;

/**
 * @implements ArrayAccess<int, Struct>
 */
class StructArray implements ArrayAccess, Struct
{
    /**
     * @var array<Struct>
     */
    protected array $messages = [];

    /**
     * @param  class-string<Struct>  $source
     */
    public function __construct(protected string $source, int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->messages[] = new $source;
        }
    }

    public function pack(): string
    {
        return implode('', array_map(fn (Struct $message) => $message->pack(), $this->messages));
    }

    public function unpack(string $buffer): void
    {
        foreach ($this->messages as $message) {
            $read = substr($buffer, 0, $message->length());
            $buffer = substr($buffer, $message->length());
            $message->unpack($read);
        }
    }

    public function length(): int
    {
        return count($this->messages) * (new $this->source)->length();
    }

    /**
     * @return Struct[]
     */
    public function messages(): array
    {
        return $this->messages;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->messages);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->messages[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->messages[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->messages[$offset]);
    }
}
