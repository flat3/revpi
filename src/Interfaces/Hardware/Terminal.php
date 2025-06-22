<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

/**
 * Interface Terminal
 *
 * Represents terminal communication capabilities common to hardware serial interfaces.
 * Extends Device, Ioctl, and Stream interfaces.
 */
interface Terminal extends Device, Ioctl, Stream
{
    /**
     * Get the input baud rate stored in the termios buffer.
     *
     * @param  string  &$buffer  The termios structure buffer (passed by reference).
     * @return int The input baud rate, or -1 on failure.
     */
    public function cfgetispeed(string &$buffer): int;

    /**
     * Get the output baud rate stored in the termios buffer.
     *
     * @param  string  &$buffer  The termios structure buffer (passed by reference).
     * @return int The output baud rate, or -1 on failure.
     */
    public function cfgetospeed(string &$buffer): int;

    /**
     * Set the input baud rate in the termios buffer.
     *
     * @param  string  &$buffer  The termios structure buffer (passed by reference).
     * @param  int  $speed  The new input baud rate to set.
     * @return int Returns 0 on success, -1 on failure.
     */
    public function cfsetispeed(string &$buffer, int $speed): int;

    /**
     * Set the output baud rate in the termios buffer.
     *
     * @param  string  &$buffer  The termios structure buffer (passed by reference).
     * @param  int  $speed  The new output baud rate to set.
     * @return int Returns 0 on success, -1 on failure.
     */
    public function cfsetospeed(string &$buffer, int $speed): int;

    /**
     * Discard data in the input/output queue.
     *
     * @param  int  $queue_selector  Selector indicating which queue(s) to flush (e.g., TCIFLUSH, TCOFLUSH).
     * @return int Returns 0 on success, or -1 on failure.
     */
    public function tcflush(int $queue_selector): int;

    /**
     * Wait until all output written to the terminal is transmitted.
     *
     * @return int Returns 0 on success, or -1 on failure.
     */
    public function tcdrain(): int;

    /**
     * Transmit a continuous stream of zero bits for a specific duration.
     *
     * @param  int  $duration  Duration to send the break (in 0.25s units), default is 0 for 0.25 seconds.
     * @return int Returns 0 on success, or -1 on failure.
     */
    public function tcsendbreak(int $duration = 0): int;
}
