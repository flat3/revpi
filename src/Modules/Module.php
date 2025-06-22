<?php

declare(strict_types=1);

namespace Flat3\RevPi\Modules;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Events\PollingEvent;
use Flat3\RevPi\Interfaces\Module as ModuleInterface;
use Flat3\RevPi\Interfaces\ProcessImage;
use Flat3\RevPi\Interfaces\SerialPort;
use Flat3\RevPi\Monitors\Monitor;
use Illuminate\Support\Facades\Event;
use Revolt\EventLoop;

abstract class Module implements ModuleInterface
{
    protected ?string $pollingCallbackId = null;

    protected float $frequency = Constants::f20Hz;

    public function __construct(protected ProcessImage $processImage) {}

    public function getProcessImage(): ProcessImage
    {
        return $this->processImage;
    }

    public function getSerialPort(string $devicePath): SerialPort
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

    public function monitor(Monitor $monitor): void
    {
        Event::listen(PollingEvent::class, function () use ($monitor) {
            static $previous = null;

            $next = $this->processImage->readVariable($monitor->name);

            if ($previous !== null && $previous !== $next) {
                EventLoop::defer(fn () => $monitor->evaluate($previous, $next));
            }

            $previous = $next;
        });
    }
}
