<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\SerialPort\PortConfiguration;

interface SerialPort
{
    public function configure(PortConfiguration $configuration): void;
}