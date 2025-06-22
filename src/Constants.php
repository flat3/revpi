<?php

declare(strict_types=1);

namespace Flat3\RevPi;

final class Constants
{
    public const BlockSize = 8192;

    public const f1Hz = 1;

    public const f20Hz = 1 / 20;

    public const f25Hz = 1 / 25;

    public const f50Hz = 1 / 50;

    public const f60Hz = 1 / 60;

    public const O_RDWR = 0x02;

    public const O_NONBLOCK = 0x0800;

    public const O_NOCTTY = 0x0100;
}
