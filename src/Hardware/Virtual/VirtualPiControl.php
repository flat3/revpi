<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Virtual;

use Flat3\RevPi\Contracts\PiControl;
use Flat3\RevPi\Exceptions\IoctlFailedException;
use Flat3\RevPi\Hardware\Interop\StructArray;
use Flat3\RevPi\Hardware\ProcessImage\Command;
use Flat3\RevPi\Hardware\ProcessImage\DataType;
use Flat3\RevPi\Hardware\ProcessImage\Device;
use Flat3\RevPi\Hardware\ProcessImage\Ioctl\DeviceInfoStruct;
use Flat3\RevPi\Hardware\ProcessImage\Ioctl\ValueStruct;
use Flat3\RevPi\Hardware\ProcessImage\Ioctl\VariableStruct;
use Flat3\RevPi\Hardware\ProcessImage\ModuleType;
use Flat3\RevPi\Hardware\ProcessImage\Variable;
use Illuminate\Support\Collection;

class VirtualPiControl extends VirtualBlockDevice implements PiControl
{
    /** @var Collection<int, Device> */
    protected Collection $devices;

    /** @var Collection<string,Variable> */
    protected Collection $variables;

    public function __construct()
    {
        $this->devices = collect();
        $this->variables = collect();
        $this->reset();
    }

    public function createVariable(Variable $variable): void
    {
        $max = $this->variables->pluck('address')->max() ?? 0;
        assert(is_int($max));
        $variable->address = $max + 2;
        $this->variables[$variable->name] = $variable;
    }

    public function createDevice(Device $device): void
    {
        $this->devices[$device->address] = $device;
    }

    public function ioctl(int $request, ?string &$argp = null): int
    {
        $command = Command::tryFrom($request);

        if ($command === null) {
            return -1;
        }

        if ($command === Command::GetDeviceInfo) {
            $device = $this->devices[0];
            $deviceInfo = new DeviceInfoStruct;
            $deviceInfo->address = $device->address;
            $deviceInfo->serialNumber = $device->serialNumber;
            $deviceInfo->moduleType = $device->moduleType->value;
            $argp = $deviceInfo->pack();

            return 0;
        }

        if ($command === Command::GetDeviceInfoList) {
            assert($argp !== null);

            $deviceInfoList = new StructArray(DeviceInfoStruct::class, 20);
            $deviceInfoList->unpack($argp);

            foreach ($this->devices->values() as $index => $device) {
                $deviceInfo = new DeviceInfoStruct;
                $deviceInfo->serialNumber = $device->serialNumber;
                $deviceInfo->moduleType = $device->moduleType->value;
                $deviceInfo->address = $device->address;
                $deviceInfoList[$index] = $deviceInfo;
            }

            $argp = $deviceInfoList->pack();

            return count($deviceInfoList->messages());
        }

        if ($command === Command::FindVariable) {
            assert($argp !== null);

            $variableInfo = new VariableStruct;
            $variableInfo->unpack($argp);

            if (! $this->variables->has($variableInfo->varName)) {
                return -1;
            }

            $variableInfo->address = $this->variables[$variableInfo->varName]->address;
            $variableInfo->length = $this->variables[$variableInfo->varName]->type->value;
            $argp = $variableInfo->pack();

            return 0;
        }

        if ($command === Command::SetValue) {
            assert($argp !== null);

            $valueMessage = new ValueStruct;
            $valueMessage->unpack($argp);

            if ($valueMessage->address < 0 || $valueMessage->address >= strlen($this->memory)) {
                return -1;
            }

            if ($valueMessage->bit < 0 || $valueMessage->bit > 7) {
                return -1;
            }

            if ($valueMessage->value !== 0 && $valueMessage->value !== 1) {
                return -1;
            }

            $byte = $this->memory[$valueMessage->address];
            $ord = ord($byte);

            if ($valueMessage->value !== 0) {
                $ord |= (1 << $valueMessage->bit);
            } else {
                $ord &= ~(1 << $valueMessage->bit);
            }

            $this->memory[$valueMessage->address] = chr($ord);

            return 0;
        }

        if ($command === Command::GetValue) {
            assert($argp !== null);

            $valueMessage = new ValueStruct;
            $valueMessage->unpack($argp);

            $byte = $this->memory[$valueMessage->address];
            $ord = ord($byte);
            $valueMessage->value = ($ord >> $valueMessage->bit) & 1;
            $argp = $valueMessage->pack();

            return 0;
        }

        if ($command === Command::Reset) { // @phpstan-ignore identical.alwaysTrue
            $this->reset();

            return 0;
        }

        throw new IoctlFailedException; // @phpstan-ignore deadCode.unreachable
    }

    protected function reset(): void
    {
        $this->memory = str_repeat("\0", 4096);

        $device = new Device;
        $device->address = 0;
        $device->serialNumber = 999999;
        $device->moduleType = ModuleType::VIRTUAL;
        $this->createDevice($device);
        $this->createVariable(new Variable('RevPiLED', DataType::Word));
    }
}
