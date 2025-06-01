<?php

declare(strict_types=1);

namespace Flat3\RevPi\IO;

class OutputIO extends IO
{
    public function set(int|bool $value): void
    {
        $this->module->getProcessImage()->writeVariable($this->name, $value);
    }

    public function reset(): void
    {
        $this->module->getProcessImage()->writeVariable($this->name, $this->default());
    }
}
