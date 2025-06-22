<?php

declare(strict_types=1);

namespace Flat3\RevPi\IO;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\Monitor;

abstract class IO
{
    public function __construct(
        protected readonly string $name,
        protected readonly int|bool|null $default,
        protected readonly Module $module
    ) {}

    public function get(): int|bool
    {
        return $this->module->getProcessImage()->readVariable($this->name);
    }

    public function monitor(Monitor $monitor, callable $callback): void
    {
        $this->module->monitor($this->name, $monitor, $callback);
    }

    public function default(): int|bool
    {
        return $this->default ?? 0;
    }
}
