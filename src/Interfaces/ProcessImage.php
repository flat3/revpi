<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\ProcessImage\Device;
use Illuminate\Support\Collection;

/**
 * Interface ProcessImage
 *
 * Provides an abstraction for interacting with a Revolution Pi process image (PiControl).
 */
interface ProcessImage
{
    /**
     * Get the underlying PiControl device interface.
     *
     * @return PiControl Instance of the PiControl hardware interface.
     */
    public function getDevice(): PiControl;

    /**
     * Read the value of a variable from the process image.
     *
     * @param  string  $variable  The name of the variable to read.
     * @return int|bool The value of the variable, as integer or boolean.
     */
    public function readVariable(string $variable): int|bool;

    /**
     * Write a value to a variable in the process image.
     *
     * @param  string  $variable  The name of the variable to write.
     * @param  int|bool  $value  The value to write to the variable.
     */
    public function writeVariable(string $variable, int|bool $value): void;

    /**
     * Dump the entire process image to a string representation (e.g., for diagnostics).
     *
     * @return string The dumped process image as a string.
     */
    public function dumpImage(): string;

    /**
     * Get information about the currently configured device.
     *
     * @return Device The device information.
     */
    public function getDeviceInfo(): Device;

    /**
     * Get a collection of all device info entries in the process image.
     *
     * @return Collection<int, Device> A collection mapping device indices to Device objects.
     */
    public function getDeviceInfoList(): Collection;

    /**
     * Reset the process image or its state.
     */
    public function reset(): void;

    /**
     * Get the module associated with the process image.
     *
     * @return Module The module object.
     */
    public function getModule(): Module;
}
