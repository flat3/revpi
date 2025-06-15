<?php

declare(strict_types=1);

namespace Flat3\RevPi;

use Flat3\RevPi\Commands\Dump;
use Flat3\RevPi\Commands\Generate;
use Flat3\RevPi\Commands\GetLed;
use Flat3\RevPi\Commands\Info;
use Flat3\RevPi\Commands\Run;
use Flat3\RevPi\Commands\SetLed;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Flat3\RevPi\Hardware\Hardware\PiControl::class, Hardware\Local\LocalPiControlDevice::class);
        $this->app->bind(Flat3\RevPi\Hardware\Hardware\Terminal::class, Hardware\Local\LocalTerminalDevice::class);

        $this->app->bind(Interfaces\ProcessImage::class, ProcessImage\ProcessImage::class);
        $this->app->bind(Interfaces\SerialPort::class, SerialPort\SerialPort::class);

        $this->app->bind(Interfaces\Modules\Connect5::class, Modules\Connect5::class);
        $this->app->bind(Interfaces\Modules\Compact::class, Modules\Compact::class);
        $this->app->bind(Interfaces\Modules\Virtual::class, Modules\Virtual::class);

        $this->app->singleton(Interfaces\Module::class, fn () => app(Interfaces\ProcessImage::class)->getModule());
    }

    public function boot(): void
    {
        $this->commands([
            GetLed::class,
            SetLed::class,
            Run::class,
            Info::class,
            Dump::class,
            Generate::class,
        ]);
    }
}
