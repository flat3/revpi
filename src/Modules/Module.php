<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Events\PollingEvent;
use Flat3\RevPi\Interfaces\Module as ModuleInterface;
use Flat3\RevPi\Interfaces\Monitor;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Interfaces\SerialPort;
use Illuminate\Support\Facades\Event;
use Revolt\EventLoop;

abstract class Module implements ModuleInterface
{
    protected ?string $pollingCallbackId = null;

    protected float $frequency = Constants::f25Hz;

    protected ?ProcessImage $processImage = null;

    public function getProcessImage(): ProcessImage
    {
        if ($this->processImage instanceof ProcessImage) {
            return $this->processImage;
        }

        $this->processImage = app(ProcessImage::class);

        return $this->processImage;
    }

    public function getSerialPort(string $devicePath = '/dev/ttyRS485'): SerialPort
    {
        return app(SerialPort::class, ['devicePath' => $devicePath]);
    }

    public function resume(float $frequency = Constants::f20Hz): void
    {
        $this->frequency = $frequency;

        if ($this->pollingCallbackId !== null) {
            $this->cancel();
        }

        $this->pollingCallbackId = EventLoop::repeat($this->frequency, function () {
            PollingEvent::dispatch();
        });
    }

    public function cancel(): void
    {
        if ($this->pollingCallbackId === null) {
            return;
        }

        EventLoop::cancel($this->pollingCallbackId);
        $this->pollingCallbackId = null;
    }

    public function monitor(string $variable, Monitor $monitor, callable $callback): void
    {
        Event::listen(PollingEvent::class, function () use ($callback, $variable, $monitor) {
            $next = $this->getProcessImage()->readVariable($variable);

            if ($monitor->evaluate($next)) {
                $callback($next);
            }
        });
    }
}
