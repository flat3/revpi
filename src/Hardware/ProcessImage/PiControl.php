<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\ProcessImage;

use Flat3\RevPi\Contracts\PiControl as PiControlContract;
use Flat3\RevPi\Hardware\Support\DeviceFileDescriptor;

class PiControl extends DeviceFileDescriptor implements PiControlContract {}
