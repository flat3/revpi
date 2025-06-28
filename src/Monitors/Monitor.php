<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Revolt\EventLoop;

abstract class Monitor
{
    abstract public function evaluate(int|bool $next): bool;

    public static function debounce(callable $callback, int $milliseconds): callable
    {
        $timerId = null;

        return function (...$args) use (&$timerId, $callback, $milliseconds) {
            if ($timerId !== null) {
                EventLoop::cancel($timerId);
            }

            $timerId = EventLoop::delay($milliseconds / 1000, function () use (&$timerId, $callback, $args) {
                $callback(...$args);
                $timerId = null;
            });
        };
    }

    public static function throttle(callable $callback, int $milliseconds): callable
    {
        $throttling = false;

        return function (...$args) use (&$throttling, $callback, $milliseconds) {
            if ($throttling) {
                return;
            }

            $throttling = true;
            $callback(...$args);

            EventLoop::delay($milliseconds / 1000, function () use (&$throttling) {
                $throttling = false;
            });
        };
    }
}
