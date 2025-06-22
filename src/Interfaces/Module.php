<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Led\LedPosition;
use Flat3\RevPi\Monitors\Monitor;

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
     * @param  Monitor  $monitor  The monitor instance to attach.
     */
    public function monitor(Monitor $monitor): void;

    /**
     * Get a serial port instance for communication on the specified device path.
     *
     * @param  string  $devicePath  The path to the serial device (e.g. "/dev/ttyS0").
     * @return SerialPort The serial port instance.
     */
    public function getSerialPort(string $devicePath): SerialPort;
}
