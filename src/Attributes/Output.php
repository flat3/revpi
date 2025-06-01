<?php

namespace Flat3\RevPi\Attributes;

use Attribute;
use Flat3\RevPi\Interfaces\Module;
use Flat3\RevPi\IO\IO;
use Flat3\RevPi\IO\OutputIO;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Output extends Tag
{
    public function tag(Module $module): IO
    {
        return app(OutputIO::class, ['name' => $this->name, 'default' => $this->default, 'module' => $module]);
    }
}
