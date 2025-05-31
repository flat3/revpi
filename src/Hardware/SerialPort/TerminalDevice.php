<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

use FFI;
use Flat3\RevPi\Contracts\TerminalDevice as TerminalIOContract;
use Flat3\RevPi\Hardware\PosixDevice\HardwarePosixDevice;

class TerminalDevice extends HardwarePosixDevice implements TerminalIOContract
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

    public function stream_open(int $fd): mixed
    {
        $stream = fopen("php://fd/{$fd}", 'r+b');
        assert($stream !== false);
        stream_set_blocking($stream, false);

        return $stream;
    }
}
