<?php

declare(strict_types=1);

namespace Flat3\RevPi\Exceptions;

class VariableNotFoundException extends ProcessImageException
{
    public function __construct(public string $variable)
    {
        parent::__construct('Variable not found');
    }
}
