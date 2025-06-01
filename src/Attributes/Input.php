<?php

namespace Flat3\RevPi\Attributes;

use Attribute;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\IO\InputIO;
use Flat3\RevPi\IO\IO;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Input extends Tag
{
    public function tag(Module $module): IO
    {
        return app(InputIO::class, ['name' => $this->name, 'default' => $this->default, 'module' => $module]);
    }
}
