<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\PosixDevice;

abstract class Ioctl implements IoctlContract
{
    /**
     * @return array<int|string,string>
     */
    abstract public function definition(): array;

    public function length(): int
    {
        return strlen($this->pack());
    }

    public function pack(): string
    {
        $format = '';
        $data = [];

        foreach ($this->definition() as $field => $type) {
            if (! is_numeric($field)) {
                $d = $this->{$field}; // @phpstan-ignore property.dynamicName
                $d = is_array($d) ? $d : [$d];
                $data = array_merge($data, $d);
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

        $groups = [];

        foreach ($data as $key => $value) {
            if (preg_match('/^(.*?)(\d+)$/', $key, $m) > 0) {
                /** @var array<int, string> $m */
                $base = $m[1];
                $idx = (int) $m[2] - 1;
                $groups[$base][$idx] = $value;
            } else {
                $groups[$key] = $value;
            }
        }

        foreach ($groups as &$arr) {
            if (is_array($arr)) {
                ksort($arr);
                $arr = array_values($arr);
            }
        }

        foreach ($groups as $field => $value) {
            $this->{$field} = $value;  // @phpstan-ignore property.dynamicName
        }
    }
}
