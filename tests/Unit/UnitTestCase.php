<?php

declare(strict_types=1);

namespace Flat3\RevPi\Tests\Unit;

use Flat3\RevPi\Contracts\PiControl;
use Flat3\RevPi\Hardware\BaseModule;
use Flat3\RevPi\Hardware\Virtual;
use Flat3\RevPi\Hardware\Virtual\VirtualPiControl;
use Flat3\RevPi\Tests\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(BaseModule::class, Virtual::class);
        $this->app->singleton(VirtualPiControl::class);
        $this->app->singleton(PiControl::class, VirtualPiControl::class);
    }
}
