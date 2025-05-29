<?php

namespace Flat3\RevPi\Attributes;

use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Hardware\IO\IO;

abstract class Tag
{
    public function __construct(public string $name, public null|int|bool $default = null) {}

    abstract public function tag(BaseModule $module): IO;
}
