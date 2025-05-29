<?php

declare(strict_types=1);

namespace Flat3\RevPi\Exceptions;

class AttributeNotFoundException extends BaseException
{
    public function __construct(public string $variable)
    {
        parent::__construct('Attribute not found');
    }
}
