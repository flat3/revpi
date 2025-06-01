<?php

namespace Flat3\RevPi\Attributes;

use Attribute;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\IO\IO;
use Flat3\RevPi\IO\MemoryIO;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Memory extends Tag
{
    public function tag(Module $module): IO
    {
        return app(MemoryIO::class, ['name' => $this->name, 'default' => $this->default, 'module' => $module]);
    }
}
