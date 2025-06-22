<?php

declare(strict_types=1);

namespace Flat3\RevPi\ProcessImage;

use Flat3\RevPi\Exceptions\NotImplementedException;
use Flat3\RevPi\Exceptions\OverflowException;
use Flat3\RevPi\Exceptions\PosixDeviceException;
use Flat3\RevPi\Exceptions\ProcessImageException;
use Flat3\RevPi\Exceptions\UnderflowException;
use Flat3\RevPi\Exceptions\VariableNotFoundException;
use Flat3\RevPi\Hardware\HasDeviceIoctl;
use Flat3\RevPi\Hardware\StructArray;
use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\Interfaces\Modules\Compact;
use Flat3\RevPi\Interfaces\Modules\Connect5;
use Flat3\RevPi\Interfaces\Modules\Virtual;
use Flat3\RevPi\Interfaces\ProcessImage as ProcessImageInterface;
use Flat3\RevPi\ProcessImage\Ioctl\DeviceInfoStruct;
use Flat3\RevPi\ProcessImage\Ioctl\ValueStruct;
use Flat3\RevPi\ProcessImage\Ioctl\VariableStruct;
use Illuminate\Support\Collection;

class ProcessImage implements ProcessImageInterface
{
    use HasDeviceIoctl;

    public function __construct(protected PiControl $device) {}

    public function getDevice(): PiControl
    {
        return $this->device;
    }

    public function writeVariable(string $variable, int|bool $value): void
    {
        $value = (int) $value;

        $variable = $this->findVariable($variable);

        if ($value < 0) {
            throw new UnderflowException;
        }

        if ($value > pow(2, $variable->length) - 1) {
            throw new OverflowException;
        }

        if ($variable->length === 1) {
            $valueMessage = new ValueStruct;
            $valueMessage->address = $variable->address;
            $valueMessage->bit = $variable->bit;
            $valueMessage->value = $value;
            $valueMessage->address += intdiv($valueMessage->bit, 8);
            $valueMessage->bit %= 8;
            $this->ioctl(Command::SetValue, $valueMessage);

            return;
        }

        $ret = $this->device->lseek($variable->address, 0);

        if ($ret < 0) {
            throw new PosixDeviceException('lseek failed');
        }

        $length = (int) ($variable->length / 8);

        $buffer = pack(match ($length) {
            1 => 'C',
            2 => 'v',
            4 => 'V',
            default => throw new ProcessImageException('Invalid data size'),
        }, $value);

        $written = $this->device->write($buffer, $length);

        if ($written !== $length) {
            throw new PosixDeviceException('write failed');
        }
    }

    public function readVariable(string $variable): bool|int
    {
        $variable = $this->findVariable($variable);

        if ($variable->length === 1) {
            $valueMessage = new ValueStruct;
            $valueMessage->address = $variable->address;
            $valueMessage->bit = $variable->bit;
            $valueMessage->address += intdiv($valueMessage->bit, 8);
            $valueMessage->bit %= 8;
            $this->ioctl(Command::GetValue, $valueMessage);

            return (bool) $valueMessage->value;
        }

        $ret = $this->device->lseek($variable->address, 0);

        if ($ret < 0) {
            throw new PosixDeviceException('lseek failed');
        }

        $length = (int) ($variable->length / 8);

        $buffer = '';

        $read = $this->device->read($buffer, $length);

        if ($read !== $length) {
            throw new PosixDeviceException('read failed');
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
    }

    public function dumpImage(): string
    {
        $buffer = '';
        $length = 512;

        $this->device->lseek(0, SEEK_SET);

        while (true) {
            $buf = '';
            $read = $this->device->read($buf, $length);
            $buffer .= $buf;

            if ($read !== $length) {
                break;
            }
        }

        return $buffer;
    }

    public function findVariable(string $variable): VariableStruct
    {
        $message = new VariableStruct;
        $message->varName = $variable;

        try {
            $this->ioctl(Command::FindVariable, $message);
        } catch (PosixDeviceException) {
            throw new VariableNotFoundException($variable);
        }

        return $message;
    }

    public function getDeviceInfo(): Device
    {
        $message = new DeviceInfoStruct;

        $this->ioctl(Command::GetDeviceInfo, $message);

        return Device::fromMessage($message);
    }

    public function getDeviceInfoList(): Collection
    {
        $messageArray = new StructArray(DeviceInfoStruct::class, 20);
        $count = $this->ioctl(Command::GetDeviceInfoList, $messageArray);

        /** @var Collection<int, DeviceInfoStruct> $deviceMessages */
        $deviceMessages = collect($messageArray->messages());

        return $deviceMessages
            ->take($count)
            ->map(fn (DeviceInfoStruct $message): Device => Device::fromMessage($message));
    }

    public function reset(): void
    {
        $this->ioctl(Command::Reset);
    }

    public function getModule(): Module
    {
        return match ($this->getDeviceInfo()->moduleType) {
            ModuleType::KUNBUS_FW_DESCR_TYP_PI_COMPACT => app(Compact::class),
            ModuleType::KUNBUS_FW_DESCR_TYP_PI_CONNECT_5 => app(Connect5::class),
            ModuleType::VIRTUAL => app(Virtual::class),
            default => throw new NotImplementedException,
        };
    }
}
