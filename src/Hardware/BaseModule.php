<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Contracts\BaseModule as BaseModuleContract;
use Flat3\RevPi\Contracts\ProcessImage;
use Flat3\RevPi\Events\PollingEvent;
use Flat3\RevPi\Monitors\Trigger;
use Illuminate\Support\Facades\Event;
use Revolt\EventLoop;

abstract class BaseModule implements BaseModuleContract
{
    protected ?string $pollingCallbackId = null;

    protected float $frequency = Constants::f20Hz;

    public function __construct(protected ProcessImage $processImage) {}

    public function image(): ProcessImage
    {
        return $this->processImage;
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

    public function monitor(Trigger $monitor): void
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
