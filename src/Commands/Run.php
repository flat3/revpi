<?php

declare(strict_types=1);

namespace Flat3\RevPi\Commands;

use Flat3\RevPi\Interfaces\Module;
use Illuminate\Console\Command;
use Revolt\EventLoop;

class Run extends Command
{
    protected $description = 'Run the program';

    protected $signature = 'revpi:run';

    public function handle(): void
    {
        app(Module::class)->resume();
        EventLoop::run();
    }
}
