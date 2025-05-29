<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage\Message;

abstract class Message implements MessageInterface
{
    /**
     * @return array<int|string,string>
     */
    abstract public function definition(): array;

    public function length(): int
    {
        return strlen($this->pack());
    }

    protected function format(): string
    {
        $format = [];

        foreach ($this->definition() as $field => $type) {
            if (is_numeric($field)) {
                $format[] = $type;
            } else {
                $format[] = $type.$field;
            }
        }

        return implode('/', $format);
    }

    public function pack(): string
    {
        $format = '';
        $data = [];

        foreach ($this->definition() as $field => $type) {
            if (! is_numeric($field)) {
                $data[] = $this->{$field};
            }

            $format .= $type;
        }

        return pack($format, ...$data);
    }

    public function unpack(string $buffer): void
    {
        $format = [];

        foreach ($this->definition() as $field => $type) {
            if (is_numeric($field)) {
                $format[] = $type;
            } else {
                $format[] = $type.$field;
            }
        }

        $format = implode('/', $format);

        $data = unpack($format, $buffer);

        assert($data !== false);

        foreach ($data as $field => $value) {
            $this->{$field} = $value;
        }
    }
}
