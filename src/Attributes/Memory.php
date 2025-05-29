<?php

namespace Flat3\RevPi\Attributes;

use Attribute;
use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Hardware\IO\IO;
use Flat3\RevPi\Hardware\IO\MemoryIO;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Memory extends Tag
{
    public function tag(BaseModule $module): IO
    {
        return app(MemoryIO::class, ['name' => $this->name, 'default' => $this->default, 'module' => $module]);
    }
}
