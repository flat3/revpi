<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests;

use Flat3\RevPi\Events\PollingEvent;
use Flat3\RevPi\Hardware\Virtual\VirtualPiControlDevice;
use Flat3\RevPi\Hardware\Virtual\VirtualTerminalDevice;
use Flat3\RevPi\Interfaces\Hardware\PiControl;
use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\Modules\Module;
use Flat3\RevPi\Modules\Virtual;
use Flat3\RevPi\ProcessImage\DataType;
use Flat3\RevPi\ProcessImage\Variable;
use Flat3\RevPi\ServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Revolt\EventLoop;

class TestCase extends BaseTestCase
{
    /**
     * @var Application
     */
    protected $app; // @phpstan-ignore property.phpDocType

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function loop(int $times, int $wait = 10): void
    {
        EventLoop::repeat(0, function () use ($wait, $times) {
            static $iterations = 0;

            if (++$iterations === $times) {
                EventLoop::getDriver()->stop();
            }

            usleep($wait * 1000);

            PollingEvent::dispatch();
        });

        EventLoop::run();
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (! $this instanceof UsesVirtualEnvironment) {
            $this->markTestSkippedWhen(! file_exists('/dev/piControl0'), 'No hardware found');

            return;
        }

        $this->app->singleton(VirtualPiControlDevice::class);
        $this->app->singleton(VirtualTerminalDevice::class);
        $this->app->bind(Module::class, Virtual::class);
        $this->app->bind(PiControl::class, VirtualPiControlDevice::class);
        $this->app->bind(Terminal::class, VirtualTerminalDevice::class);

        $control = app(VirtualPiControlDevice::class);

        $control->createVariable(new Variable('Test_0', DataType::Word));

        foreach (range(1, 48) as $position) {
            $name = 'OutBit_'.$position;
            $control->createVariable(new Variable($name, DataType::Bool));
        }

        foreach (range(1, 10) as $position) {
            $name = 'OutByte_'.$position;
            $control->createVariable(new Variable($name, DataType::Byte));
        }

        foreach (range(1, 4) as $position) {
            $name = 'OutWord_'.$position;
            $control->createVariable(new Variable($name, DataType::Word));
        }

        foreach (range(1, 2) as $position) {
            $name = 'OutDWord_'.$position;
            $control->createVariable(new Variable($name, DataType::DWord));
        }
    }
}
