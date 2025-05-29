<?php

declare(strict_types=1);

namespace Flat3\RevPi;

use Flat3\RevPi\Commands\Dump;
use Flat3\RevPi\Commands\Generate;
use Flat3\RevPi\Commands\GetLed;
use Flat3\RevPi\Commands\Info;
use Flat3\RevPi\Commands\Run;
use Flat3\RevPi\Commands\SetLed;
use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Contracts\Compact;
use Flat3\RevPi\Contracts\Connect5;
use Flat3\RevPi\Contracts\PiControl;
use Flat3\RevPi\Contracts\ProcessImage;
use Flat3\RevPi\Contracts\Virtual;
use Flat3\RevPi\Hardware\ProcessImage\PiProcessImage;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PiControl::class, Hardware\ProcessImage\PiControl::class);
        $this->app->singleton(ProcessImage::class, PiProcessImage::class);
        $this->app->singleton(BaseModule::class, fn () => app(ProcessImage::class)->getModule());

        $this->app->bind(Connect5::class, Hardware\Connect5::class);
        $this->app->bind(Compact::class, Hardware\Compact::class);
        $this->app->bind(Virtual::class, Hardware\Virtual::class);
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
