<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Local;

use FFI;
use Flat3\RevPi\Interfaces\Hardware\Terminal;

class LocalTerminalDevice extends LocalDevice implements Terminal
{
    public function cfsetispeed(string &$buffer, int $speed): int
    {
        $buf = $this->ffi->new(sprintf('char[%d]', strlen($buffer))); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $buffer, strlen($buffer));
        $ret = $this->ffi->cfsetispeed($buf, $speed); // @phpstan-ignore method.notFound
        $buffer = FFI::string($buf, strlen($buffer));

        return $ret;
    }

    public function cfsetospeed(string &$buffer, int $speed): int
    {
        $buf = $this->ffi->new(sprintf('char[%d]', strlen($buffer))); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $buffer, strlen($buffer));
        $ret = $this->ffi->cfsetospeed($buf, $speed); // @phpstan-ignore method.notFound
        $buffer = FFI::string($buf, strlen($buffer));

        return $ret;
    }

    public function cfgetispeed(string &$buffer): int
    {
        $buf = $this->ffi->new(sprintf('char[%d]', strlen($buffer))); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $buffer, strlen($buffer));

        return $this->ffi->cfgetispeed($buf); // @phpstan-ignore method.notFound
    }

    public function cfgetospeed(string &$buffer): int
    {
        $buf = $this->ffi->new(sprintf('char[%d]', strlen($buffer))); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $buffer, strlen($buffer));

        return $this->ffi->cfgetospeed($buf); // @phpstan-ignore method.notFound
    }

    public function tcflush(int $queue_selector): int
    {
        return $this->ffi->tcflush($this->fd, $queue_selector);
    }

    public function tcdrain(): int
    {
        return $this->ffi->tcdrain($this->fd);
    }

    public function tcsendbreak(int $duration = 0): int
    {
        return $this->ffi->tcsendbreak($this->fd, $duration);
    }
}
