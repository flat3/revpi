<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage;

use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Contracts\Compact;
use Flat3\RevPi\Contracts\Connect5;
use Flat3\RevPi\Contracts\PiControl;
use Flat3\RevPi\Contracts\ProcessImage as ProcessImageContract;
use Flat3\RevPi\Contracts\Virtual;
use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Exceptions\OverflowException;
use Flat3\RevPi\Exceptions\PiControlException;
use Flat3\RevPi\Exceptions\ProcessImageException;
use Flat3\RevPi\Exceptions\UnderflowException;
use Flat3\RevPi\Exceptions\VariableNotFoundException;
use Flat3\RevPi\Hardware\PosixDevice\IoctlArray;
use Flat3\RevPi\Hardware\PosixDevice\IoctlInterface;
use Flat3\RevPi\Hardware\ProcessImage\Ioctl\SDeviceInfoIoctl;
use Flat3\RevPi\Hardware\ProcessImage\Ioctl\ValueIoctl;
use Flat3\RevPi\Hardware\ProcessImage\Ioctl\VariableIoctl;
use Illuminate\Support\Collection;

class ProcessImage implements ProcessImageContract
{
    public function __construct(protected PiControl $device, protected string $devicePath = '/dev/piControl0') {}

    protected function open(): int
    {
        $descriptor = $this->device->open($this->devicePath, 2);

        if ($descriptor < 0) {
            throw new PiControlException('open failed');
        }

        return $descriptor;
    }

    protected function close(int $descriptor): void
    {
        $this->device->close($descriptor);
    }

    protected function command(Command $command, ?IoctlInterface $message = null): int
    {
        $descriptor = $this->open();

        try {
            if ($message === null) {
                $ret = $this->device->ioctl($descriptor, $command->value);

                if ($ret < 0) {
                    throw new PiControlException('ioctl failed');
                }

                return $ret;
            }

            $buf = $message->pack();
            $ret = $this->device->ioctl($descriptor, $command->value, $buf);

            if ($ret < 0) {
                throw new PiControlException('ioctl failed');
            }

            assert(is_string($buf));

            $message->unpack($buf);
        } finally {
            $this->close($descriptor);
        }

        return $ret;
    }

    public function writeVariable(string $variable, int|bool $value): void
    {
        $descriptor = $this->open();

        $value = (int) $value;

        try {
            $variable = $this->findVariable($variable);

            if ($value < 0) {
                throw new UnderflowException;
            }

            if ($value > pow(2, $variable->length) - 1) {
                throw new OverflowException;
            }

            if ($variable->length === 1) {
                $valueMessage = new ValueIoctl;
                $valueMessage->address = $variable->address;
                $valueMessage->bit = $variable->bit;
                $valueMessage->value = $value;
                $valueMessage->address += intdiv($valueMessage->bit, 8);
                $valueMessage->bit %= 8;
                $this->command(Command::SetValue, $valueMessage);

                return;
            }

            $ret = $this->device->lseek($descriptor, $variable->address, 0);

            if ($ret < 0) {
                throw new PiControlException('lseek failed');
            }

            $length = (int) ($variable->length / 8);

            $buffer = pack(match ($length) {
                1 => 'C',
                2 => 'v',
                4 => 'V',
                default => throw new ProcessImageException('Invalid data size'),
            }, $value);

            $written = $this->device->write($descriptor, $buffer, $length);

            if ($written !== $length) {
                throw new PiControlException('write failed');
            }
        } finally {
            $this->close($descriptor);
        }
    }

    public function readVariable(string $variable): bool|int
    {
        $descriptor = $this->open();

        try {
            $variable = $this->findVariable($variable);

            if ($variable->length === 1) {
                $valueMessage = new ValueIoctl;
                $valueMessage->address = $variable->address;
                $valueMessage->bit = $variable->bit;
                $valueMessage->address += intdiv($valueMessage->bit, 8);
                $valueMessage->bit %= 8;
                $this->command(Command::GetValue, $valueMessage);

                return (bool) $valueMessage->value;
            }

            $ret = $this->device->lseek($descriptor, $variable->address, 0);

            if ($ret < 0) {
                throw new PiControlException('lseek failed');
            }

            $length = (int) ($variable->length / 8);

            $buffer = '';

            $read = $this->device->read($descriptor, $buffer, $length);

            if ($read !== $length) {
                throw new PiControlException('read failed');
            }

            $data = unpack(
                format: match ($length) {
                    1 => 'C',
                    2 => 'v',
                    4 => 'V',
                    default => throw new ProcessImageException('Invalid data size'),
                },
                string: $buffer
            );

            assert($data !== false);

            return $data[1];
        } finally {
            $this->close($descriptor);
        }
    }

    public function dumpImage(): string
    {
        $descriptor = $this->open();

        $buffer = '';
        $length = 512;

        while (true) {
            $buf = '';
            $read = $this->device->read($descriptor, $buf, $length);
            $buffer .= $buf;

            if ($read !== $length) {
                break;
            }
        }

        $this->close($descriptor);

        return $buffer;
    }

    protected function findVariable(string $variable): VariableIoctl
    {
        $message = new VariableIoctl;
        $message->varName = $variable;

        try {
            $this->command(Command::FindVariable, $message);
        } catch (PiControlException) {
            throw new VariableNotFoundException($variable);
        }

        return $message;
    }

    public function getDeviceInfo(): Device
    {
        $message = new SDeviceInfoIoctl;

        $this->command(Command::GetDeviceInfo, $message);

        return Device::fromMessage($message);
    }

    public function getDeviceInfoList(): Collection
    {
        $messageArray = new IoctlArray(SDeviceInfoIoctl::class, 20);
        $count = $this->command(Command::GetDeviceInfoList, $messageArray);

        /** @var Collection<int, SDeviceInfoIoctl> $deviceMessages */
        $deviceMessages = collect($messageArray->messages());

        return $deviceMessages
            ->take($count)
            ->map(fn (SDeviceInfoIoctl $message): Device => Device::fromMessage($message));
    }

    public function reset(): void
    {
        $this->command(Command::Reset);
    }

    public function getModule(): BaseModule
    {
        return match ($this->getDeviceInfo()->moduleType) {
            ModuleType::KUNBUS_FW_DESCR_TYP_PI_COMPACT => app(Compact::class),
            ModuleType::KUNBUS_FW_DESCR_TYP_PI_CONNECT_5 => app(Connect5::class),
            ModuleType::VIRTUAL => app(Virtual::class),
            default => throw new NotImplementedException,
        };
    }
}
