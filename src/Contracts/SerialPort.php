<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\SerialPort\BaudRate;
use Flat3\RevPi\Hardware\SerialPort\Parity;

interface SerialPort
{
    public function setSpeed(BaudRate $rate): void;

    public function getSpeed(): BaudRate;

    public function setTermination(bool $enabled): void;

    public function getTermination(): bool;

    public function setParity(Parity $parity): void;

    public function getParity(): Parity;
}
