<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Flat3\RevPi\Contracts\BaseModule;
use Illuminate\Console\Command;
use Revolt\EventLoop;

class Run extends Command
{
    protected $description = 'Run the program';

    protected $signature = 'revpi:run';

    public function handle(): void
    {
        app(BaseModule::class)->resume();
        EventLoop::run();
    }
}
