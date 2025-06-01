<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\Local;

use Flat3\RevPi\Constants;
use Flat3\RevPi\Hardware\Interfaces\PiControlInterface;

class LocalPiControlDevice extends LocalDevice implements PiControlInterface
{
    public function __construct(protected string $devicePath = '/dev/piControl0')
    {
        parent::__construct();

        $this->open($this->devicePath, Constants::O_RDWR);
    }

    public function __destruct()
    {
        $this->close();
    }
}
