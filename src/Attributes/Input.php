<?php

namespace Flat3\RevPi\Attributes;

use Attribute;
use Flat3\RevPi\Contracts\BaseModule;
use Flat3\RevPi\Hardware\IO\InputIO;
use Flat3\RevPi\Hardware\IO\IO;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Input extends Tag
{
    public function tag(BaseModule $module): IO
    {
        return app(InputIO::class, ['name' => $this->name, 'default' => $this->default, 'module' => $module]);
    }
}
