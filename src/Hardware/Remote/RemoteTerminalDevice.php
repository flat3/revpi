<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Remote;

use Flat3\RevPi\Interfaces\Hardware\Terminal;

class RemoteTerminalDevice extends RemoteCharacterDevice implements Terminal
{
    public function cfgetispeed(string &$buffer): int
    {
        /** @var array{buffer: string, return: int} $response */
        $response = $this->device->request('cfgetispeed', ['buffer' => $buffer])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function cfgetospeed(string &$buffer): int
    {
        /** @var array{buffer: string, return: int} $response */
        $response = $this->device->request('cfgetospeed', ['buffer' => $buffer])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function cfsetispeed(string &$buffer, int $speed): int
    {
        /** @var array{buffer: string, return: int} $response */
        $response = $this->device->request('cfsetispeed', ['buffer' => $buffer, 'speed' => $speed])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function cfsetospeed(string &$buffer, int $speed): int
    {
        /** @var array{buffer: string, return: int} $response */
        $response = $this->device->request('cfsetospeed', ['buffer' => $buffer, 'speed' => $speed])->await();
        $buffer = $response['buffer'];

        return $response['return'];
    }

    public function tcflush(int $queue_selector): int
    {
        return (int) $this->device->request('tcflush', ['queue_selector' => $queue_selector])->await();
    }

    public function tcdrain(): int
    {
        return (int) $this->device->request('tcdrain')->await();
    }

    public function tcsendbreak(int $duration = 0): int
    {
        return (int) $this->device->request('tcsendbreak', ['duration' => $duration])->await();
    }
}
