<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces;

use Flat3\RevPi\Interfaces\Hardware\Terminal;
use Flat3\RevPi\SerialPort\BaudRate;
use Flat3\RevPi\SerialPort\ControlFlag;
use Flat3\RevPi\SerialPort\DataBits;
use Flat3\RevPi\SerialPort\InputFlag;
use Flat3\RevPi\SerialPort\LocalFlag;
use Flat3\RevPi\SerialPort\OutputFlag;
use Flat3\RevPi\SerialPort\Parity;
use Flat3\RevPi\SerialPort\QueueSelector;
use Flat3\RevPi\SerialPort\RS485Flag;
use Flat3\RevPi\SerialPort\StopBits;

interface SerialPort
{
    public function getDevice(): Terminal;

    public function setSpeed(BaudRate $rate): static;

    public function getSpeed(): BaudRate;

    public function setFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): static;

    public function clearFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): static;

    public function getFlag(InputFlag|OutputFlag|ControlFlag|LocalFlag|RS485Flag $flag): bool;

    public function setParity(Parity $parity): static;

    public function getParity(): Parity;

    public function setDataBits(DataBits $bits): static;

    public function getDataBits(): DataBits;

    public function setStopBits(StopBits $bits): static;

    public function getStopBits(): StopBits;

    public function onReadable(callable $callback): string;

    public function read(int $count = 1024): string;

    public function write(string $data): void;

    public function flush(QueueSelector $selector): void;

    public function drain(): void;

    public function break(int $duration = 0): void;
}
