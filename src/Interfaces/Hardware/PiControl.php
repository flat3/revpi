<?php

declare(strict_types=1);

namespace Flat3\RevPi\Interfaces\Hardware;

/**
 * Interface PiControl
 *
 * Represents a PiControl hardware component, combining device operations,
 * IOCTL (input/output control), and seek capabilities.
 *
 * @see Device
 * @see Ioctl
 * @see Seek
 */
interface PiControl extends Device, Ioctl, Seek {}
