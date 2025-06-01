<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Local;

use FFI;
use Flat3\RevPi\Hardware\Interfaces\Device;

class LocalDevice implements Device
{
    protected FFI $ffi;

    protected ?int $fd = null;

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
int tcflush(int fd, int queue_selector);
int tcdrain(int fd);
int tcsendbreak(int fd, int duration);
EOF, 'libc.so.6');
    }

    public function open(string $pathname, int $flags): int
    {
        if ($this->fd !== null) {
            return 0;
        }

        $this->fd = $this->ffi->open($pathname, $flags); // @phpstan-ignore method.notFound

        assert($this->fd !== null);

        return $this->fd;
    }

    public function close(): int
    {
        if ($this->fd === null) {
            return -1;
        }

        $result = $this->ffi->close($this->fd); // @phpstan-ignore method.notFound

        $this->fd = null;

        return $result;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function read(string &$buffer, int $count): int
    {
        $buf = $this->ffi->new("char[$count]"); // @phpstan-ignore staticMethod.dynamicCall
        $read = $this->ffi->read($this->fd, $buf, $count); // @phpstan-ignore method.notFound
        assert($buf instanceof FFI\CData);
        $buffer = FFI::string($buf, $read);

        return $read;
    }

    public function write(string $buffer, int $count): int
    {
        $buf = $this->ffi->new("char[$count]"); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $buffer, $count);

        return $this->ffi->write($this->fd, $buf, $count); // @phpstan-ignore method.notFound
    }

    public function lseek(int $offset, int $whence): int
    {
        return $this->ffi->lseek($this->fd, $offset, $whence); // @phpstan-ignore method.notFound
    }

    public function ioctl(int $request, ?string &$argp = null): int
    {
        if ($argp === null) {
            return $this->ffi->ioctl($this->fd, $request, null); // @phpstan-ignore method.notFound
        }

        $buf = $this->ffi->new(sprintf('char[%d]', strlen($argp))); // @phpstan-ignore staticMethod.dynamicCall
        assert($buf instanceof FFI\CData);
        FFI::memcpy($buf, $argp, strlen($argp));
        $ret = $this->ffi->ioctl($this->fd, $request, $buf); // @phpstan-ignore method.notFound
        $argp = FFI::string($buf, strlen($argp));

        return $ret;
    }

    public function fdopen(): mixed
    {
        $stream = fopen("php://fd/{$this->fd}", 'r+b');
        assert($stream !== false);
        stream_set_blocking($stream, false);

        return $stream;
    }
}
