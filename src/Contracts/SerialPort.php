<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\SerialPort\BaudRate;
use Flat3\RevPi\Hardware\SerialPort\DataBits;
use Flat3\RevPi\Hardware\SerialPort\Parity;
use Flat3\RevPi\Hardware\SerialPort\StopBits;

interface SerialPort
{
    public function setSpeed(BaudRate $rate): static;

    public function getSpeed(): BaudRate;

    public function setTermination(bool $enabled): static;

    public function getTermination(): bool;

    public function setParity(Parity $parity): static;

    public function getParity(): Parity;

    public function setDataBits(DataBits $bits): static;

    public function getDataBits(): DataBits;

    public function setStopBits(StopBits $bits): static;

    public function getStopBits(): StopBits;

    public function onReadable(callable $callback): static;
}
