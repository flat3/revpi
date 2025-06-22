<?php

declare(strict_types=1);

namespace Flat3\RevPi\Pictory;

class IO
{
    public string $name;

    public int|bool|null $default;

    public ?string $unit;

    public int $offset;

    public bool $exported;

    public string $displayOrder;

    public ?string $comment;

    /**
     * @param  array<int, string|int|bool|null>  $definition
     */
    public static function fromPictory(array $definition): self
    {
        $io = new self;

        $io->name = (string) $definition[0];

        $io->unit = match ($definition[2]) {
            '1' => 'bool',
            default => 'int',
        };

        $io->default = match ($io->unit) {
            'bool' => (bool) ($definition[1]),
            default => (int) $definition[1],
        };

        $io->exported = (bool) $definition[4];
        $io->displayOrder = (string) $definition[5];
        $io->comment = $definition[6] === null ? null : (string) $definition[6];

        return $io;
    }

    /**
     * @return array<string, int|bool|string>
     */
    public function attributeArgs(): array
    {
        $args = [
            'name' => $this->name,
        ];

        if ($this->default !== null) {
            $args['default'] = $this->default;
        }

        return $args;
    }
}
