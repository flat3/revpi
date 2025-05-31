<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\SerialPort\BaudRate;
use Flat3\RevPi\Hardware\SerialPort\DataBits;
use Flat3\RevPi\Hardware\SerialPort\Parity;
use Flat3\RevPi\Hardware\SerialPort\StopBits;

interface SerialPort
{
    public function setSpeed(BaudRate $rate): void;

    public function getSpeed(): BaudRate;

    public function setTermination(bool $enabled): void;

    public function getTermination(): bool;

    public function setParity(Parity $parity): void;

    public function getParity(): Parity;

    public function setDataBits(DataBits $bits): void;

    public function getDataBits(): DataBits;

    public function setStopBits(StopBits $bits): void;

    public function getStopBits(): StopBits;
}
