<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

use Revolt\EventLoop;

abstract class Monitor
{
    protected ?int $rateLimit = null;

    protected RateLimit $rateLimitType = RateLimit::None;

    public function debounce(int $milliseconds): static
    {
        $this->rateLimit = $milliseconds;
        $this->rateLimitType = RateLimit::Debounce;

        return $this;
    }

    public function throttle(int $milliseconds): static
    {
        $this->rateLimit = $milliseconds;
        $this->rateLimitType = RateLimit::Throttle;

        return $this;
    }

    public function wrap(callable $callback): callable
    {
        assert($this->rateLimitType === RateLimit::None || is_int($this->rateLimit));

        return match ($this->rateLimitType) {
            RateLimit::None => $callback,
            RateLimit::Debounce => $this->_debouncer($callback, (int) $this->rateLimit),
            RateLimit::Throttle => $this->_throttler($callback, (int) $this->rateLimit),
        };
    }

    abstract public function evaluate(int|bool $next): bool;

    protected function _debouncer(callable $callback, int $milliseconds): callable
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

    protected function _throttler(callable $callback, int $milliseconds): callable
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
