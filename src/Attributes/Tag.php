<?php

namespace Flat3\RevPi\Attributes;

use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\IO\IO;

abstract class Tag
{
    public function __construct(public string $name, public null|int|bool $default = null) {}

    abstract public function tag(Module $module): IO;
}
