<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\ProcessImage\Device;
use Illuminate\Console\Command;

class Info extends Command
{
    protected $description = 'Display device information';

    protected $signature = 'revpi:info';

    public function handle(): void
    {
        $devices = app(Module::class)->getProcessImage()->getDeviceInfoList();

        $this->table(['Index', 'Type', 'Address', 'Serial number', 'Version', 'Active', 'State'],
            $devices->map(fn (Device $device, int $index) => [
                $index,
                $device->moduleType->name(),
                $device->address,
                $device->serialNumber,
                $device->version(),
                $device->active ? 'Yes' : 'No',
                $index === 0 ? '-' : $device->moduleState->name(),
            ]));
    }
}
