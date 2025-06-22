<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

interface Monitor
{
    public function evaluate(int|bool|null $next): bool;
}
