<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

interface PiControl extends Device, Ioctl, Seek {}
