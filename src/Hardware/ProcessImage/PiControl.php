<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage;

use Flat3\RevPi\Contracts\PiControl as PiControlContract;
use Flat3\RevPi\Hardware\PosixDevice\HardwarePosixDevice;

class PiControl extends HardwarePosixDevice implements PiControlContract {}
