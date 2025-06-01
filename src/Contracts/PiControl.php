<?php

declare(strict_types=1);

namespace Flat3\RevPi\Contracts;

use Flat3\RevPi\Hardware\Interfaces\DeviceIoctlInterface;
use Flat3\RevPi\Hardware\Interfaces\DeviceSeekInterface;
use Flat3\RevPi\Hardware\Interfaces\PosixDeviceInterface;

interface PiControl extends DeviceIoctlInterface, DeviceSeekInterface, PosixDeviceInterface {}
