<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Led\LedPosition;

/**
 * Interface Module
 *
 * Represents a hardware module in the RevPi ecosystem, providing methods for controlling
 * LEDs, accessing process images, monitoring, and serial port access.
 */
interface Module
{
    /**
     * Get a LED instance at the specified position.
     *
     * @param  LedPosition  $position  The position of the LED to retrieve.
     * @return Led The LED instance at the specified position.
     */
    public function getLed(LedPosition $position): Led;

    /**
     * Obtain the process image associated with this module.
     *
     * @return ProcessImage The process image of the module.
     */
    public function getProcessImage(): ProcessImage;

    /**
     * Resume normal operation for the module after a pause or reset.
     */
    public function resume(): void;

    /**
     * Attach a monitor to the module for status tracking or event watching.
     *
     * @param  string  $variable  The variable to monitor.
     * @param  Monitor  $monitor  The monitor instance to attach.
     * @param  callable  $callback  The callback to call.
     */
    public function monitor(string $variable, Monitor $monitor, callable $callback): void;

    /**
     * Get a serial port instance for communication on the specified device path.
     *
     * @param  string  $devicePath  The path to the serial device (e.g. "/dev/ttyRS485").
     * @return SerialPort The serial port instance.
     */
    public function getSerialPort(string $devicePath = '/dev/ttyRS485'): SerialPort;
}
