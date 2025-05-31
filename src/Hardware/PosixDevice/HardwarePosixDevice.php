<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\PosixDevice;

use FFI;

class HardwarePosixDevice implements PosixDevice
{
    protected FFI $ffi;

    public function __construct()
    {
        $this->ffi = FFI::cdef(<<<'EOF'
int ioctl(int fd, unsigned long request, void* argp);
int open(const char *pathname, int flags);
off_t lseek(int fd, off_t offset, int whence);
ssize_t read(int fd, void *buf, size_t count);
ssize_t write(int fd, const void *buf, size_t count);
int cfgetispeed(void* argp);
int cfgetospeed(void* argp);
int cfsetispeed(void* argp, unsigned int speed);
int cfsetospeed(void* argp, unsigned int speed);
int close(int fd);
EOF, 'libc.so.6');
    }

    public function open(string $pathname, int $flags): int
    {
        return $this->ffi->open($pathname, $flags); // @phpstan-ignore method.notFound
    }

    public function close(int $fd): int
    {
        return $this->ffi->close($fd); // @phpstan-ignore method.notFound
    }

    public function read(int $fd, string &$buffer, int $count): int
    {
        $buf = $this->ffi->new("char[$count]"); // @phpstan-ignore staticMethod.dynamicCall
        $read = $this->ffi->read($fd, $buf, $count); // @phpstan-ignore method.notFound
        assert($buf instanceof FFI\CData);
        $buffer = FFI::string($buf, $read);

        return $read;
    }

    public function write(int $fd, string $buffer, int $count): int
    {
        $buf = $this->ffi->new("char[$count]"); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $buffer, $count);

        return $this->ffi->write($fd, $buf, $count); // @phpstan-ignore method.notFound
    }

    public function lseek(int $fd, int $offset, int $whence): int
    {
        return $this->ffi->lseek($fd, $offset, $whence); // @phpstan-ignore method.notFound
    }

    public function ioctl(int $fd, int $request, ?string &$argp = null): int
    {
        if ($argp === null) {
            return $this->ffi->ioctl($fd, $request, null); // @phpstan-ignore method.notFound
        }

        $buf = $this->ffi->new(sprintf('char[%d]', strlen($argp))); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $argp, strlen($argp));
        $ret = $this->ffi->ioctl($fd, $request, $buf); // @phpstan-ignore method.notFound
        $argp = FFI::string($buf, strlen($argp));

        return $ret;
    }
}
