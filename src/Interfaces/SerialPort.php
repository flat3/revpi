<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\SerialPort\BaudRate;
use Flat3\RevPi\SerialPort\ControlFlag;
use Flat3\RevPi\SerialPort\DataBits;
use Flat3\RevPi\SerialPort\InputFlag;
use Flat3\RevPi\SerialPort\LocalFlag;
use Flat3\RevPi\SerialPort\OutputFlag;
use Flat3\RevPi\SerialPort\Parity;
use Flat3\RevPi\SerialPort\QueueSelector;
use Flat3\RevPi\SerialPort\RS485Flag;
use Flat3\RevPi\SerialPort\StopBits;

/**
 * Interface SerialPort
 *
 * Represents a generic serial port interface for configuring and
 * handling serial communication operations.
 */
interface SerialPort
{
    /**
     * Get the device terminal associated with the serial port.
     *
     * @return Terminal The terminal device object.
     */
    public function getDevice(): Terminal;

    /**
     * Set the baud rate (speed) for the serial port communication.
     *
     * @param  BaudRate  $rate  Baud rate to set.
     */
    public function setSpeed(BaudRate $rate): static;

    /**
     * Get the current baud rate (speed) of the serial port.
     *
     * @return BaudRate The current baud rate.
     */
    public function getSpeed(): BaudRate;

    /**
     * Set a flag on the serial port.
     *
     * @param  InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag  $flag  The flag to set.
     */
    public function setFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): static;

    /**
     * Clear a flag from the serial port.
     *
     * @param  InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag  $flag  The flag to clear.
     */
    public function clearFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): static;

    /**
     * Check whether a flag is set on the serial port.
     *
     * @param  InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag  $flag  The flag to check.
     * @return bool True if the flag is set, false otherwise.
     */
    public function getFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): bool;

    /**
     * Set the parity configuration for the serial port.
     *
     * @param  Parity  $parity  Parity mode to set.
     */
    public function setParity(Parity $parity): static;

    /**
     * Get the current parity configuration of the serial port.
     *
     * @return Parity Current parity setting.
     */
    public function getParity(): Parity;

    /**
     * Set the number of data bits for serial communication.
     *
     * @param  DataBits  $bits  Number of data bits to use.
     */
    public function setDataBits(DataBits $bits): static;

    /**
     * Get the number of data bits configured for the serial port.
     *
     * @return DataBits Configured data bits.
     */
    public function getDataBits(): DataBits;

    /**
     * Set the number of stop bits for the serial port communication.
     *
     * @param  StopBits  $bits  Number of stop bits to use.
     */
    public function setStopBits(StopBits $bits): static;

    /**
     * Get the number of stop bits configured for the serial port.
     *
     * @return StopBits Configured stop bits.
     */
    public function getStopBits(): StopBits;

    /**
     * Register a callback to be invoked when the port is readable.
     *
     * @param  callable  $callback  Callback to invoke on readable event.
     * @return string An ID or reference for the registered callback.
     */
    public function onReadable(callable $callback): string;

    /**
     * Read data from the serial port.
     *
     * @param  int  $count  Maximum number of bytes to read (default: 1024).
     * @return string Data read from the port.
     */
    public function read(int $count = 1024): string;

    /**
     * Write data to the serial port.
     *
     * @param  string  $data  Data to write.
     */
    public function write(string $data): void;

    /**
     * Flush the serial port buffer according to the given queue selector.
     *
     * @param  QueueSelector  $selector  Selector specifying which buffers to flush.
     */
    public function flush(QueueSelector $selector): void;

    /**
     * Wait until all output written to the serial port has been transmitted.
     */
    public function drain(): void;

    /**
     * Generate a break condition on the serial port line.
     *
     * @param  int  $duration  The break duration in milliseconds, 0 for default.
     */
    public function break(int $duration = 0): void;
}
