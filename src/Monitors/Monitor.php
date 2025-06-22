<?php

declare(strict_types=1);

namespace Flat3\RevPi\Monitors;

abstract class Monitor
{
    /**
     * @param  callable  $callback
     */
    public function __construct(public string $name, public mixed $callback) {}

    abstract public function evaluate(mixed $previous, mixed $next): void;
}
