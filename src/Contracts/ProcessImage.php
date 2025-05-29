<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\ProcessImage\Device;
use Illuminate\Support\Collection;

interface ProcessImage
{
    public function readVariable(string $variable): int|bool;

    public function writeVariable(string $variable, int|bool $value): void;

    public function dumpImage(): string;

    public function getDeviceInfo(): Device;

    /**
     * @return Collection<int, Device>
     */
    public function getDeviceInfoList(): Collection;

    public function reset(): void;

    public function getModule(): BaseModule;
}
